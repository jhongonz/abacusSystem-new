<?php

namespace App\Http\Controllers;

use Core\Profile\Domain\Contracts\ProfileDataTransformerContract;
use Core\Profile\Domain\Contracts\ProfileFactoryContract;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class ProfileController extends Controller implements HasMiddleware
{
    private ProfileFactoryContract $profileFactory;
    private ProfileManagementContract $profileService;
    private ProfileDataTransformerContract $profileDataTransformer;
    private DataTables $dataTable;

    public function __construct(
        ProfileFactoryContract $profileFactory,
        ProfileManagementContract $profileService,
        ProfileDataTransformerContract $profileDataTransformer,
        DataTables $dataTable,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);
        $this->profileFactory = $profileFactory;
        $this->profileService = $profileService;
        $this->profileDataTransformer = $profileDataTransformer;
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
        } catch (Exception $exception) {
            $this->logger->error('Profile can not be updated with id: '. $profileId->value());
            return response()->json(status:Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(status: Response::HTTP_CREATED);
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

    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return Middleware|array
     */
    public static function middleware(): Middleware|array
    {
        return [
            new Middleware('auth'),
            new Middleware('only.ajax-request', only:[
                'getProfiles',
            ]),
        ];
    }
}
