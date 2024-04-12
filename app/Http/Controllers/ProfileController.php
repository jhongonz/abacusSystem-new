<?php

namespace App\Http\Controllers;

use App\Events\Profile\ProfileUpdatedOrDeletedEvent;
use App\Events\User\RefreshModulesSession;
use App\Http\Requests\Profile\StoreProfileRequest;
use Core\Profile\Domain\Contracts\ModuleFactoryContract;
use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Contracts\ProfileDataTransformerContract;
use Core\Profile\Domain\Contracts\ProfileFactoryContract;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use JetBrains\PhpStorm\NoReturn;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class ProfileController extends Controller implements HasMiddleware
{
    private ModuleFactoryContract $moduleFactory;
    private ProfileFactoryContract $profileFactory;
    private ProfileManagementContract $profileService;
    private ProfileDataTransformerContract $profileDataTransformer;
    private ModuleManagementContract $moduleService;
    private DataTables $dataTable;

    public function __construct(
        ModuleFactoryContract $moduleFactory,
        ProfileFactoryContract $profileFactory,
        ProfileManagementContract $profileService,
        ProfileDataTransformerContract $profileDataTransformer,
        ModuleManagementContract $moduleService,
        DataTables $dataTable,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);
        $this->moduleFactory = $moduleFactory;
        $this->profileFactory = $profileFactory;
        $this->profileService = $profileService;
        $this->profileDataTransformer = $profileDataTransformer;
        $this->moduleService = $moduleService;
        $this->dataTable = $dataTable;
    }

    public function index():JsonResponse|string
    {
        $view = view('profile.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }

    /**
     * @throws Exception
     */
    public function getProfiles(Request $request): JsonResponse
    {
        $profiles = $this->profileService->searchProfiles($request->filters);
        return $this->prepareListProfiles($profiles);
    }

    public function changeStateProfile(Request $request): JsonResponse
    {
        $profileId = $this->profileFactory->buildProfileId($request->id);
        $profile = $this->profileService->searchProfileById($profileId);

        if ($profile->state()->isNew() || $profile->state()->isInactived()) {
            $profile->state()->activate();
        } else if ($profile->state()->isActived()) {
            $profile->state()->inactive();
        }

        $dataUpdate['state'] = $profile->state()->value();

        try {
            $this->profileService->updateProfile($profileId, $dataUpdate);
            ProfileUpdatedOrDeletedEvent::dispatch($profileId);
            RefreshModulesSession::dispatch();
        } catch (Exception $exception) {
            $this->logger->error('Profile can not be updated with id: '. $profileId->value());
            return response()->json(status:Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(status: Response::HTTP_CREATED);
    }

    public function deleteProfile(int $id): JsonResponse
    {
        $profileId = $this->profileFactory->buildProfileId($id);
        $this->profileService->deleteProfile($profileId);

        ProfileUpdatedOrDeletedEvent::dispatch($profileId);

        return response()->json(status:Response::HTTP_OK);
    }

    public function getProfile(null|int $id = null): JsonResponse
    {
        $profile = null;
        if (!is_null($id)) {
            $profile = $this->profileService->searchProfileById(
                $this->profileFactory->buildProfileId($id)
            );
        }

        $modules = $this->moduleService->searchModules();
        $privileges = $this->retrievePrivilegesProfile($profile, $modules);

        $view = view('profile.profile-form')
            ->with('id', $id)
            ->with('profile', $profile)
            ->with('modules', $modules)
            ->with('privileges', $privileges)
            ->render();

        return $this->renderView($view);
    }

    public function storeProfile(StoreProfileRequest $request): JsonResponse
    {
        $profileId = $this->profileFactory->buildProfileId($request->id);

        try {
            $method = (is_null($profileId->value())) ? 'createProfile' : 'updateProfile';
            $this->{$method}($request, $profileId);
            ProfileUpdatedOrDeletedEvent::dispatch($profileId);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            return response()->json(['msg'=>'Ha ocurrido un error al guardar el registro, consulte con su administrador de sistemas'],
            Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(status:Response::HTTP_CREATED);
    }

    #[NoReturn] private function createProfile(StoreProfileRequest $request, ProfileId $profileId): void
    {
        $profile = $this->profileFactory->buildProfile(
            $profileId,
            $this->profileFactory->buildProfileName($request->name)
        );
        $profile->description()->setValue($request->description);

        $modulesAggregator = $this->getModulesAggregator($request);
        $profile->setModulesAggregator($modulesAggregator);

        $this->profileService->createProfile($profile);
    }

    private function updateProfile(StoreProfileRequest $request, ProfileId $profileId): void
    {
        $modulesAggregator = $this->getModulesAggregator($request);

        $dataUpdate = [
            'name' => $request->name,
            'description' => $request->description,
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
            foreach($profiles as $item) {
                $dataProfiles[] = $this->profileDataTransformer->write($item)->readToShare();
            }
        }

        $datatable = $this->dataTable->collection(collect($dataProfiles));
        $datatable->addColumn('tools', function(array $item) {
            return $this->retrieveMenuOptionHtml($item);
        });

        return $datatable->escapeColumns([])->toJson();
    }

    public function retrievePrivilegesProfile(null|Profile $profile, Modules $modules): array
    {
        $modulesToProfile = (!is_null($profile)) ? $profile->modulesAggregator() : [];
        $parents = config('menu.options');
        $privileges = [];

        foreach($parents as $index => $item) {
            $modulesParent = $modules->moduleElementsOfKey($index);
            $privileges[$index]['menu'] = $item;
            /**@var Module $module*/
            foreach ($modulesParent as $module) {
                if (!$module->state()->isInactived()) {
                    $privileges[$index]['children'][] = [
                        'module' => $module,
                        'selected' => in_array($module->id()->value(), $modulesToProfile)
                    ];
                }
            }
        }

        return $privileges;
    }

    private function getModulesAggregator(Request $request) : array
    {
        $modulesAggregator = [];
        foreach ($request->modules as $item) {
            $modulesAggregator[] = $item['id'];
        }

        return $modulesAggregator;
    }

    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return Middleware|array
     */
    public static function middleware(): Middleware|array
    {
        return [
            new Middleware(['auth','verify-session']),
            new Middleware('only.ajax-request', only:[
                'getProfiles','getProfile'
            ]),
        ];
    }
}
