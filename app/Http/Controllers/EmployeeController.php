<?php

namespace App\Http\Controllers;

use App\Events\Employee\EmployeeUpdateOrDeletedEvent;
use App\Events\User\UserUpdateOrDeleteEvent;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Traits\MultimediaTrait;
use App\Traits\UserTrait;
use Core\Employee\Domain\Contracts\EmployeeDataTransformerContract;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\User;
use DateTime;
use Exception;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\Factory as ViewFactory;
use Intervention\Image\Interfaces\ImageManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class EmployeeController extends Controller implements HasMiddleware
{
    use UserTrait;
    use MultimediaTrait;

    private EmployeeManagementContract $employeeService;
    private EmployeeDataTransformerContract $employeeDataTransformer;
    private UserManagementContract $userService;
    private ProfileManagementContract $profileService;
    private DataTables $dataTable;

    public function __construct(
        EmployeeManagementContract $employeeService,
        EmployeeDataTransformerContract $employeeDataTransformer,
        UserManagementContract $userService,
        ProfileManagementContract $profileService,
        DataTables $dataTable,
        ImageManagerInterface $imageManager,
        ViewFactory $viewFactory,
        LoggerInterface $logger,
        Hasher $hasher,
    ) {
        parent::__construct($logger, $viewFactory);
        $this->setImageManager($imageManager);
        $this->setHasher($hasher);

        $this->employeeService = $employeeService;
        $this->employeeDataTransformer = $employeeDataTransformer;
        $this->userService = $userService;
        $this->profileService = $profileService;
        $this->dataTable = $dataTable;
    }

    public function index(): JsonResponse|string
    {
        $view = $this->viewFactory->make('employee.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }

    /**
     * @throws \Yajra\DataTables\Exceptions\Exception
     */
    public function getEmployees(Request $request): JsonResponse
    {
        $employees = $this->employeeService->searchEmployees($request->input('filters'));

        return $this->prepareListEmployees($employees);
    }

    public function changeStateEmployee(Request $request): JsonResponse
    {
        $employeeId = $request->input('id');
        $employee = $this->employeeService->searchEmployeeById($employeeId);

        $employeeState = $employee->state();
        if ($employeeState->isNew() || $employeeState->isInactivated()) {
            $employeeState->activate();
        } elseif ($employeeState->isActivated()) {
            $employeeState->inactive();
        }

        $dataUpdate['state'] = $employeeState->value();

        try {
            $this->employeeService->updateEmployee($employeeId, $dataUpdate);
        } catch (Exception $exception) {
            $this->logger->error('Employee can not be updated with id: '.$employeeId, $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (! is_null($employee->userId()->value())) {
            $user = $this->userService->searchUserById(
                $employee->userId()->value()
            );

            try {
                $this->userService->updateUser($user->id()->value(), $dataUpdate);
                UserUpdateOrDeleteEvent::dispatch($user->id());
            } catch (Exception $exception) {
                $message = sprintf(
                    'User with ID: %d by employee with ID: %d can not be updated',
                    $user->id()->value(),
                    $employeeId
                );
                $this->logger->error($message, $exception->getTrace());
            }
        }

        return new JsonResponse(status:Response::HTTP_CREATED);
    }

    public function getEmployee(?int $employeeId = null): JsonResponse|string
    {
        $employee = null;
        if (! is_null($employeeId)) {
            $employee = $this->employeeService->searchEmployeeById($employeeId);
            $user = $this->userService->searchUserById($employee->userId()->value());

            $urlFile = url(self::IMAGE_PATH_FULL.$employee->image()->value()).'?v='.Str::random(10);
        }

        $profiles = $this->profileService->searchProfiles();
        $userId = (! is_null($employee)) ? $employee->userId()->value() : null;

        $view = $this->viewFactory->make('employee.employee-form')
            ->with('userId', $userId)
            ->with('employeeId', $employeeId)
            ->with('employee', $employee)
            ->with('user', $user ?? null)
            ->with('profiles', $profiles)
            ->with('image', $urlFile ?? null)
            ->render();

        return $this->renderView($view);
    }

    public function storeEmployee(StoreEmployeeRequest $request): JsonResponse
    {
        $employeeId = $request->input('employeeId');
        $userId = $request->input('userId');

        try {
            $method = (is_null($employeeId)) ? 'createEmployee' : 'updateEmployee';
            $this->{$method}($request, $employeeId, $userId);
            EmployeeUpdateOrDeletedEvent::dispatch($employeeId);
            UserUpdateOrDeleteEvent::dispatch($userId);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(
                ['msg' => 'Ha ocurrido un error al guardar el registro, consulte con su administrador de sistemas'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse(['userId' => $userId, 'employeeId' => $employeeId], Response::HTTP_CREATED);
    }

    /**
     * @throws \Yajra\DataTables\Exceptions\Exception
     */
    private function prepareListEmployees(Employees $employees): JsonResponse
    {
        $dataEmployees = [];
        if ($employees->count()) {
            /** @var Employee $item */
            foreach ($employees as $item) {
                $dataEmployees[] = $this->employeeDataTransformer->write($item)->readToShare();
            }
        }

        $collection = new Collection($dataEmployees);
        $datatable = $this->dataTable->collection($collection);
        $datatable->addColumn('tools', function (array $element): string {
            return $this->retrieveMenuOptionHtml($element);
        });

        return $datatable->escapeColumns([])->toJson();
    }

    public function setImageEmployee(Request $request): JsonResponse
    {
        $random = Str::random(10);
        $imageUrl = $this->saveImageTmp($request->file('file')->getRealPath(), $random);

        return new JsonResponse(['token' => $random, 'url' => $imageUrl], Response::HTTP_CREATED);
    }

    public function deleteEmployee(int $employeeId): JsonResponse
    {
        $employee = $this->employeeService->searchEmployeeById($employeeId);

        try {
            $this->employeeService->deleteEmployee($employeeId);
            $this->deleteImage($employee->image()->value());
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (! is_null($employee->userId()->value())) {

            try {
                $this->userService->deleteUser($employee->userId()->value());
            } catch (Exception $exception) {
                $this->logger->error($exception->getMessage(), $exception->getTrace());
            }
        }

        return new JsonResponse(status: Response::HTTP_OK);
    }

    /**
     * @throws Exception
     */
    private function updateEmployee(StoreEmployeeRequest $request, int $employeeId, int $userId): void
    {
        $birthdate = $request->input('birthdate');
        $dataUpdate = [
            'identifier' => $request->input('identifier'),
            'typeDocument' => $request->input('typeDocument'),
            'name' => $request->input('name'),
            'lastname' => $request->input('lastname'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'observations' => $request->input('observations'),
            'birthdate' => ($birthdate) ? DateTime::createFromFormat('d/m/Y', $birthdate) : $birthdate,
        ];

        $dataUpdateUser = [
            'profileId' => $request->input('profile'),
            'login' => $request->input('login'),
        ];

        $imageToken = $request->input('token');
        if (! is_null($imageToken)) {
            $filename = $this->saveImage($imageToken);
            $dataUpdate['image'] = $filename;
            $dataUpdateUser['image'] = $filename;
        }

        $this->employeeService->updateEmployee($employeeId, $dataUpdate);

        $password = $request->input('password');
        if (! is_null($password)) {
            $dataUpdateUser['password'] = $this->makeHashPassword($password);
        }

        if ($dataUpdateUser) {
            $this->userService->updateUser($userId, $dataUpdateUser);
        }
    }

    private function createEmployee(StoreEmployeeRequest $request, int $employeeId, int $userId): void
    {
        $dataEmployee = [
            'id' => $employeeId,
            'identifier' => $request->input('identifier'),
            'name' => $request->input('name'),
            'lastname' => $request->input('lastname'),
            'typeDocument' => $request->input('typeDocument'),
            'observations' => $request->input('observations'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'birthdate' => DateTime::createFromFormat('d/m/Y', $request->input('birthdate')),
        ];

        $token = $request->input('token');
        if (! is_null($token)) {
            $filename = $this->saveImage($token);
            $dataEmployee['image'] = $filename;
        }

        $this->employeeService->createEmployee([Employee::TYPE => $dataEmployee]);

        $dataUser = [
            'id' => $userId,
            'employeeId' => $employeeId,
            'profileId' => $request->input('profile'),
            'login' => $request->input('login'),
            'password' => $this->makeHashPassword($request->input('password')),
            'photo' => $filename ?? ''
        ];

        $this->userService->createUser([User::TYPE => $dataUser]);
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): Middleware|array
    {
        return [
            new Middleware(['auth', 'verify-session']),
            new Middleware('only.ajax-request', only: [
                'getEmployees', 'setImageEmployee', 'deleteEmployee', 'changeStateEmployee', 'storeEmployee',
            ]),
        ];
    }
}
