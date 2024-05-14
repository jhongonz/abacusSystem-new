<?php

namespace App\Http\Controllers;

use App\Events\Profile\ProfileUpdatedOrDeletedEvent;
use App\Events\User\RefreshModulesSession;
use App\Http\Requests\Profile\StoreProfileRequest;
use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Contracts\ProfileDataTransformerContract;
use Core\Profile\Domain\Contracts\ProfileFactoryContract;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\SharedContext\Model\ValueObjectStatus;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\Factory as ViewFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class ProfileController extends Controller implements HasMiddleware
{
    private ProfileFactoryContract $profileFactory;

    private ProfileManagementContract $profileService;

    private ProfileDataTransformerContract $profileDataTransformer;

    private ModuleManagementContract $moduleService;

    private DataTables $dataTable;
    private ViewFactory $viewFactory;

    public function __construct(
        ProfileFactoryContract $profileFactory,
        ProfileManagementContract $profileService,
        ProfileDataTransformerContract $profileDataTransformer,
        ModuleManagementContract $moduleService,
        DataTables $dataTable,
        ViewFactory $viewFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);
        $this->profileFactory = $profileFactory;
        $this->profileService = $profileService;
        $this->profileDataTransformer = $profileDataTransformer;
        $this->moduleService = $moduleService;
        $this->dataTable = $dataTable;
        $this->viewFactory = $viewFactory;
    }

    public function index(): JsonResponse|string
    {
        $view = $this->viewFactory->make('profile.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }

    /**
     * @throws Exception
     */
    public function getProfiles(Request $request): JsonResponse
    {
        $profiles = $this->profileService->searchProfiles($request->input('filters'));

        return $this->prepareListProfiles($profiles);
    }

    public function changeStateProfile(Request $request): JsonResponse
    {
        $profileId = $this->profileFactory->buildProfileId($request->input('id'));
        $profile = $this->profileService->searchProfileById($profileId);

        if ($profile->state()->isNew() || $profile->state()->isInactivated()) {
            $profile->state()->activate();
        } elseif ($profile->state()->isActivated()) {
            $profile->state()->inactive();
        }

        $dataUpdate['state'] = $profile->state()->value();

        try {
            $this->profileService->updateProfile($profileId, $dataUpdate);
            ProfileUpdatedOrDeletedEvent::dispatch($profileId);
            RefreshModulesSession::dispatch();
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(status: Response::HTTP_CREATED);
    }

    public function deleteProfile(int $id): JsonResponse
    {
        $profileId = $this->profileFactory->buildProfileId($id);

        try {
            $this->profileService->updateProfile($profileId, ['state' => ValueObjectStatus::STATE_DELETE]);
            $this->profileService->deleteProfile($profileId);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
        }

        ProfileUpdatedOrDeletedEvent::dispatch($profileId);

        return new JsonResponse(status: Response::HTTP_OK);
    }

    public function getProfile(?int $id = null): JsonResponse
    {
        $profile = null;
        if (! is_null($id)) {
            $profile = $this->profileService->searchProfileById(
                $this->profileFactory->buildProfileId($id)
            );
        }

        $modules = $this->moduleService->searchModules();
        $privileges = $this->retrievePrivilegesProfile($profile, $modules);

        $view = $this->viewFactory->make('profile.profile-form')
            ->with('id', $id)
            ->with('profile', $profile)
            ->with('modules', $modules)
            ->with('privileges', $privileges)
            ->render();

        return $this->renderView($view);
    }

    public function storeProfile(StoreProfileRequest $request): JsonResponse
    {
        $profileId = $this->profileFactory->buildProfileId($request->input('id'));

        try {
            $method = (is_null($profileId->value())) ? 'createProfile' : 'updateProfile';
            $this->{$method}($request, $profileId);
            ProfileUpdatedOrDeletedEvent::dispatch($profileId);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(
                ['msg' => 'Ha ocurrido un error al guardar el registro, consulte con su administrador de sistemas'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse(status: Response::HTTP_CREATED);
    }

    private function createProfile(StoreProfileRequest $request, ProfileId $profileId): void
    {
        $profile = $this->profileFactory->buildProfile(
            $profileId,
            $this->profileFactory->buildProfileName($request->input('name'))
        );
        $profile->description()->setValue($request->input('description'));

        $modulesAggregator = $this->getModulesAggregator($request);
        $profile->setModulesAggregator($modulesAggregator);

        $this->profileService->createProfile($profile);
    }

    private function updateProfile(StoreProfileRequest $request, ProfileId $profileId): void
    {
        $modulesAggregator = $this->getModulesAggregator($request);

        $dataUpdate = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'modules' => $modulesAggregator,
        ];

        $this->profileService->updateProfile($profileId, $dataUpdate);
    }

    /**
     * @throws \Yajra\DataTables\Exceptions\Exception
     */
    private function prepareListProfiles(Profiles $profiles): JsonResponse
    {
        $dataProfiles = [];
        if ($profiles->count()) {
            /** @var Profile $item */
            foreach ($profiles as $item) {
                $dataProfiles[] = $this->profileDataTransformer->write($item)->readToShare();
            }
        }

        $datatable = $this->dataTable->collection(collect($dataProfiles));
        $datatable->addColumn('tools', function (array $item) {
            return $this->retrieveMenuOptionHtml($item);
        });

        return $datatable->escapeColumns([])->toJson();
    }

    public function retrievePrivilegesProfile(?Profile $profile, Modules $modules): array
    {
        $modulesToProfile = (! is_null($profile)) ? $profile->modulesAggregator() : [];
        $parents = config('menu.options');
        $privileges = [];

        foreach ($parents as $index => $item) {
            $modulesParent = $modules->moduleElementsOfKey($index);
            $privileges[$index]['menu'] = $item;
            /** @var Module $module */
            foreach ($modulesParent as $module) {
                if (! $module->state()->isInactivated()) {
                    $privileges[$index]['children'][] = [
                        'module' => $module,
                        'selected' => in_array($module->id()->value(), $modulesToProfile),
                    ];
                }
            }
        }

        return $privileges;
    }

    private function getModulesAggregator(Request $request): array
    {
        $modulesAggregator = [];
        foreach ($request->input('modules') as $item) {
            $modulesAggregator[] = $item['id'];
        }

        return $modulesAggregator;
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): Middleware|array
    {
        return [
            new Middleware(['auth', 'verify-session']),
            new Middleware('only.ajax-request', only: [
                'getProfiles', 'getProfile',
            ]),
        ];
    }
}
