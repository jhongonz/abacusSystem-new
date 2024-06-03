<?php

namespace App\Http\Controllers;

use App\Events\Employee\EmployeeUpdateOrDeletedEvent;
use App\Events\User\UserUpdateOrDeleteEvent;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Traits\MultimediaTrait;
use App\Traits\UserTrait;
use Core\Employee\Domain\Contracts\EmployeeDataTransformerContract;
use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\ValueObjects\UserId;
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
    private EmployeeFactoryContract $employeeFactory;
    private EmployeeDataTransformerContract $employeeDataTransformer;
    private UserFactoryContract $userFactory;
    private UserManagementContract $userService;
    private ProfileManagementContract $profileService;
    private DataTables $dataTable;

    public function __construct(
        EmployeeManagementContract $employeeService,
        EmployeeFactoryContract $employeeFactory,
        EmployeeDataTransformerContract $employeeDataTransformer,
        UserFactoryContract $userFactory,
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
        $this->employeeFactory = $employeeFactory;
        $this->employeeDataTransformer = $employeeDataTransformer;
        $this->userFactory = $userFactory;
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
        $employeeId = $this->employeeFactory->buildEmployeeId($request->input('id'));
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
            $this->logger->error('Employee can not be updated with id: '.$employeeId->value(), $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (! is_null($employee->userId()->value())) {
            $user = $this->userService->searchUserById(
                $this->userFactory->buildId($employee->userId()->value())
            );

            try {
                $this->userService->updateUser($user->id(), $dataUpdate);
                UserUpdateOrDeleteEvent::dispatch($user->id());
            } catch (Exception $exception) {
                $message = sprintf(
                    'User with ID: %d by employee with ID: %d can not be updated',
                    $user->id()->value(),
                    $employeeId->value()
                );
                $this->logger->error($message, $exception->getTrace());
            }
        }

        return new JsonResponse(status:Response::HTTP_CREATED);
    }

    public function getEmployee(?int $id = null): JsonResponse|string
    {
        $employeeId = $this->employeeFactory->buildEmployeeId($id);
        $employee = null;

        if (! is_null($employeeId->value())) {
            $employee = $this->employeeService->searchEmployeeById($employeeId);
            $user = $this->userService->searchUserById($this->userFactory->buildId($employee->userId()->value()));

            $urlFile = url(self::IMAGE_PATH_FULL.$employee->image()->value()).'?v='.Str::random(10);
        }

        $profiles = $this->profileService->searchProfiles();
        $userId = (! is_null($employee)) ? $employee->userId()->value() : null;

        $view = $this->viewFactory->make('employee.employee-form')
            ->with('userId', $userId)
            ->with('employeeId', $employeeId->value())
            ->with('employee', $employee)
            ->with('user', $user ?? null)
            ->with('profiles', $profiles)
            ->with('image', $urlFile ?? null)
            ->render();

        return $this->renderView($view);
    }

    public function storeEmployee(StoreEmployeeRequest $request): JsonResponse
    {
        $employeeId = $this->employeeFactory->buildEmployeeId($request->input('employeeId'));
        $userId = $this->userFactory->buildId($request->input('userId'));

        try {
            $method = (is_null($employeeId->value())) ? 'createEmployee' : 'updateEmployee';
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

        return new JsonResponse(['userId' => $userId->value(), 'employeeId' => $employeeId->value()], Response::HTTP_CREATED);
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

    public function deleteEmployee(int $id): JsonResponse
    {
        $employeeId = $this->employeeFactory->buildEmployeeId($id);
        $employee = $this->employeeService->searchEmployeeById($employeeId);

        try {
            $this->employeeService->deleteEmployee($employeeId);
            $this->deleteImage($employee->image()->value());
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (! is_null($employee->userId()->value())) {

            $userId = $this->userFactory->buildId($employee->userId()->value());

            try {
                $this->userService->deleteUser($userId);
            } catch (Exception $exception) {
                $this->logger->error($exception->getMessage(), $exception->getTrace());
            }
        }

        return new JsonResponse(status: Response::HTTP_OK);
    }

    /**
     * @throws Exception
     */
    private function updateEmployee(StoreEmployeeRequest $request, EmployeeId $employeeId, UserId $userId): void
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

        if (! is_null($userId->value()) && $dataUpdateUser) {
            $this->userService->updateUser($userId, $dataUpdateUser);
        }
    }

    private function createEmployee(StoreEmployeeRequest $request, EmployeeId $employeeId, UserId $userId): void
    {
        $employee = $this->employeeFactory->buildEmployee(
            $employeeId,
            $this->employeeFactory->buildEmployeeIdentification($request->input('identifier')),
            $this->employeeFactory->buildEmployeeName($request->input('name')),
            $this->employeeFactory->buildEmployeeLastname($request->input('lastname'))
        );

        $employee->identificationType()->setValue($request->input('typeDocument'));
        $employee->observations()->setValue($request->input('observations'));
        $employee->email()->setValue($request->input('email'));
        $employee->address()->setValue($request->input('address'));
        $employee->birthdate()->setValue(DateTime::createFromFormat('d/m/Y', $request->input('birthdate')));

        $token = $request->input('token');
        if (! is_null($token)) {
            $filename = $this->saveImage($token);
            $employee->image()->setValue($filename);
        }

        $this->employeeService->createEmployee($employee);

        $user = $this->userFactory->buildUser(
            $userId,
            $this->userFactory->buildEmployeeId($employee->id()->value()),
            $this->userFactory->buildProfileId((int) $request->input('profile')),
            $this->userFactory->buildLogin($request->input('login')),
            $this->userFactory->buildPassword($this->makeHashPassword($request->input('password')))
        );
        $user->photo()->setValue($filename ?? '');
        $this->userService->createUser($user);
    }

    /**
     * Get the middleware that should be assigned to the controller.
     * @codeCoverageIgnore
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
