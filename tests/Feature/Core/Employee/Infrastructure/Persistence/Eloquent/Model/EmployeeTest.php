<?php

namespace Tests\Feature\Core\Employee\Infrastructure\Persistence\Eloquent\Model;

use Core\Employee\Infrastructure\Persistence\Eloquent\Model\Employee;
use Core\User\Infrastructure\Persistence\Eloquent\Model\User;
use Illuminate\Database\Eloquent\Relations\HasOne;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(Employee::class)]
class EmployeeTest extends TestCase
{
    private Employee $model;

    private Employee|MockObject $modelMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new Employee;
    }

    public function tearDown(): void
    {
        unset($this->model, $this->modelMock);
        parent::tearDown();
    }

    public function test_getSearchField_should_return_string(): void
    {
        $result = $this->model->getSearchField();

        $this->assertIsString($result);
        $this->assertSame($result, 'emp_search');
    }

    /**
     * @throws Exception
     */
    public function test_relationWithUser_should_return_relation(): void
    {
        $relationMock = $this->createMock(HasOne::class);

        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['newRelatedInstance', 'hasOne', 'newHasOne'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('hasOne')
            ->with(User::class, 'user__emp_id', 'emp_id')
            ->willReturn($relationMock);

        $result = $this->modelMock->relationWithUser();

        $this->assertInstanceOf(HasOne::class, $result);
        $this->assertSame($result, $relationMock);
    }

    public function test_user_should_return_model_user(): void
    {
        $result = $this->model->user();
        $this->assertInstanceOf(User::class, $result);
    }

    public function test_id_should_return_int(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_id')
            ->willReturn(1);

        $result = $this->modelMock->id();

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function test_id_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_id')
            ->willReturn(null);

        $result = $this->modelMock->id();

        $this->assertNull($result);
    }

    public function test_changeId_should_change_and_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('emp_id', 2)
            ->willReturnSelf();

        $result = $this->modelMock->changeId(2);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function test_changeId_with_null_should_change_and_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('emp_id', null)
            ->willReturnSelf();

        $result = $this->modelMock->changeId(null);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function test_identification_should_return_string(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_identification')
            ->willReturn('12345');

        $result = $this->modelMock->identification();

        $this->assertIsString($result);
        $this->assertSame($result, '12345');
    }

    public function test_identification_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_identification')
            ->willReturn(null);

        $result = $this->modelMock->identification();

        $this->assertNull($result);
    }

    public function test_changeIdentification_should_change_and_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('emp_identification', '123')
            ->willReturnSelf();

        $result = $this->modelMock->changeIdentification('123');

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function test_name_should_return_string(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_name')
            ->willReturn('Peter');

        $result = $this->modelMock->name();

        $this->assertIsString($result);
        $this->assertSame($result, 'Peter');
    }

    public function test_name_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_name')
            ->willReturn(null);

        $result = $this->modelMock->name();

        $this->assertNull($result);
    }

    public function test_changeName_should_change_and_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('emp_name', 'John')
            ->willReturnSelf();

        $result = $this->modelMock->changeName('John');

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function test_lastname_should_return_string(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_lastname')
            ->willReturn('Smith');

        $result = $this->modelMock->lastname();

        $this->assertIsString($result);
        $this->assertSame($result, 'Smith');
    }

    public function test_lastname_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_lastname')
            ->willReturn(null);

        $result = $this->modelMock->lastname();

        $this->assertNull($result);
    }

    public function test_changeLastname_should_change_and_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('emp_lastname', 'Jeter')
            ->willReturnSelf();

        $result = $this->modelMock->changeLastname('Jeter');

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function test_phone_should_return_string(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_phone_number')
            ->willReturn('12345');

        $result = $this->modelMock->phone();

        $this->assertIsString($result);
        $this->assertSame($result, '12345');
    }

    public function test_phone_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_phone_number')
            ->willReturn(null);

        $result = $this->modelMock->phone();

        $this->assertNull($result);
    }

    public function test_changePhone_should_change_and_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('emp_phone_number', '67890')
            ->willReturnSelf();

        $result = $this->modelMock->changePhone('67890');

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function test_email_should_return_string(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_email')
            ->willReturn('localhost@host.com');

        $result = $this->modelMock->email();

        $this->assertIsString($result);
        $this->assertSame($result, 'localhost@host.com');
    }

    public function test_email_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_email')
            ->willReturn(null);

        $result = $this->modelMock->email();

        $this->assertNull($result);
    }

    public function test_changeEmail_should_change_and_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('emp_email', 'test@host.com')
            ->willReturnSelf();

        $result = $this->modelMock->changeEmail('test@host.com');

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function test_address_should_return_string(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_address')
            ->willReturn('test');

        $result = $this->modelMock->address();

        $this->assertIsString($result);
        $this->assertSame($result, 'test');
    }

    public function test_address_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_address')
            ->willReturn(null);

        $result = $this->modelMock->address();

        $this->assertNull($result);
    }

    public function test_changeAddress_should_change_and_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('emp_address', 'testing')
            ->willReturnSelf();

        $result = $this->modelMock->changeAddress('testing');

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function test_state_should_return_int(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_state')
            ->willReturn(1);

        $result = $this->modelMock->state();

        $this->assertIsInt($result);
        $this->assertSame($result, 1);
    }

    public function test_changeState_should_change_and_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('emp_state', 2)
            ->willReturnSelf();

        $result = $this->modelMock->changeState(2);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    /**
     * @throws \Exception
     */
    public function test_createdAt_should_return_string(): void
    {
        $datetime = '2024-05-02 02:24:00';
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('created_at')
            ->willReturn($datetime);

        $result = $this->modelMock->createdAt();

        $this->assertInstanceOf(\DateTime::class, $result);
    }

    /**
     * @throws \Exception
     */
    public function test_createdAt_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('created_at')
            ->willReturn(null);

        $result = $this->modelMock->createdAt();

        $this->assertNull($result);
    }

    public function test_changeCreatedAt_should_change_and_return_self(): void
    {
        $datetime = new \DateTime;
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('created_at', $datetime)
            ->willReturnSelf();

        $result = $this->modelMock->changeCreatedAt($datetime);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    /**
     * @throws \Exception
     */
    public function test_updatedAt_should_return_string(): void
    {
        $datetime = '2024-05-02 02:27:00';
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('updated_at')
            ->willReturn($datetime);

        $result = $this->modelMock->updatedAt();

        $this->assertInstanceOf(\DateTime::class, $result);
    }

    /**
     * @throws \Exception
     */
    public function test_updatedAt_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('updated_at')
            ->willReturn(null);

        $result = $this->modelMock->updatedAt();

        $this->assertNull($result);
    }

    public function test_changeUpdatedAt_should_change_and_return_self(): void
    {
        $datetime = new \DateTime;
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('updated_at', $datetime)
            ->willReturnSelf();

        $result = $this->modelMock->changeUpdatedAt($datetime);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function test_search_should_return_string(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_search')
            ->willReturn('test');

        $result = $this->modelMock->search();

        $this->assertIsString($result);
        $this->assertSame($result, 'test');
    }

    public function test_search_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_search')
            ->willReturn(null);

        $result = $this->modelMock->search();

        $this->assertNull($result);
    }

    public function test_changeSearch_should_change_and_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('emp_search', 'testing')
            ->willReturnSelf();

        $result = $this->modelMock->changeSearch('testing');

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    /**
     * @throws \Exception
     */
    public function test_birthdate_should_return_string(): void
    {
        $datetime = '2024-05-02 02:24:00';
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_birthdate')
            ->willReturn($datetime);

        $result = $this->modelMock->birthdate();

        $this->assertInstanceOf(\DateTime::class, $result);
    }

    /**
     * @throws \Exception
     */
    public function test_birthdate_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_birthdate')
            ->willReturn(null);

        $result = $this->modelMock->birthdate();

        $this->assertNull($result);
    }

    public function test_changeBirthdate_should_change_and_return_self(): void
    {
        $datetime = new \DateTime;
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('emp_birthdate', $datetime)
            ->willReturnSelf();

        $result = $this->modelMock->changeBirthdate($datetime);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function test_observations_should_return_string(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_observations')
            ->willReturn('test');

        $result = $this->modelMock->observations();

        $this->assertIsString($result);
        $this->assertSame($result, 'test');
    }

    public function test_observations_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_observations')
            ->willReturn(null);

        $result = $this->modelMock->observations();

        $this->assertNull($result);
    }

    public function test_changeObservations_should_change_and_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('emp_observations', 'testing')
            ->willReturnSelf();

        $result = $this->modelMock->changeObservations('testing');

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function test_identificationType_should_return_string(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_identification_type')
            ->willReturn('test');

        $result = $this->modelMock->identificationType();

        $this->assertIsString($result);
        $this->assertSame($result, 'test');
    }

    public function test_changeIdentificationType_should_change_and_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('emp_identification_type', 'testing')
            ->willReturnSelf();

        $result = $this->modelMock->changeIdentificationType('testing');

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function test_image_should_return_string(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_image')
            ->willReturn('test');

        $result = $this->modelMock->image();

        $this->assertIsString($result);
        $this->assertSame($result, 'test');
    }

    public function test_image_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('emp_image')
            ->willReturn(null);

        $result = $this->modelMock->image();

        $this->assertNull($result);
    }

    public function test_changeImage_should_change_and_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Employee::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('emp_image', 'testing')
            ->willReturnSelf();

        $result = $this->modelMock->changeImage('testing');

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->modelMock);
    }
}
