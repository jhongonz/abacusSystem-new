<?php

namespace Tests\Feature\Core\Campus\Infrastructure\Persistence\Eloquent\Model;

use Core\Campus\Infrastructure\Persistence\Eloquent\Model\Campus;
use Core\Institution\Infrastructure\Persistence\Eloquent\Model\Institution;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(Campus::class)]
class CampusTest extends TestCase
{
    private Campus $model;
    private Campus|MockObject $modelMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new Campus();
    }

    public function tearDown(): void
    {
        unset($this->model, $this->modelMock);
        parent::tearDown();
    }

    public function testGetSearchFieldShouldReturnString(): void
    {
        $result = $this->model->getSearchField();

        $this->assertIsString($result);
        $this->assertSame($result, 'cam_search');
    }

    public function testRelationWithInstitutionShouldReturnRelation(): void
    {
        $result = $this->model->relationWithInstitution();

        $this->assertInstanceOf(BelongsTo::class, $result);
        $this->assertSame('institutions', $result->getModel()->getTable());
    }

    public function testInstitutionShouldReturnModel(): void
    {
        $result = $this->model->institution();

        $this->assertInstanceOf(Institution::class, $result);
    }

    public function testIdShouldReturnNull(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('cam_id')
            ->willReturn(null);

        $result = $this->modelMock->id();

        $this->assertNull($result);
    }

    public function testIdShouldReturnInt(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('cam_id')
            ->willReturn(1);

        $result = $this->modelMock->id();

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function testChangeIdShouldChangeAndReturnSelf(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('cam_id', 2)
            ->willReturnSelf();

        $result = $this->modelMock->changeId(2);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function testInstitutionIdShouldReturnInt(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('cam__inst_id')
            ->willReturn(1);

        $result = $this->modelMock->institutionId();

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function testChangeInstitutionIdShouldChangeAndReturnSelf(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('cam__inst_id', 2)
            ->willReturnSelf();

        $result = $this->modelMock->changeInstitutionId(2);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function testNameShouldReturnString(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('cam_name')
            ->willReturn('name-test');

        $result = $this->modelMock->name();

        $this->assertIsString($result);
        $this->assertSame('name-test', $result);
    }

    public function testChangeNameShouldChangeAndReturnSelf(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('cam_name', 'name-updated')
            ->willReturnSelf();

        $result = $this->modelMock->changeName('name-updated');

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function testAddressShouldReturnNull(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('cam_address')
            ->willReturn(null);

        $result = $this->modelMock->address();

        $this->assertNull($result);
    }

    public function testAddressShouldReturnString(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('cam_address')
            ->willReturn('address-test');

        $result = $this->modelMock->address();

        $this->assertIsString($result);
        $this->assertSame('address-test', $result);
    }

    public function testChangeAddressShouldChangeAndReturnSelf(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('cam_address', 'address-updated')
            ->willReturnSelf();

        $result = $this->modelMock->changeAddress('address-updated');

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function testPhoneShouldReturnNull(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('cam_phone')
            ->willReturn(null);

        $result = $this->modelMock->phone();

        $this->assertNull($result);
    }

    public function testPhoneShouldReturnString(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('cam_phone')
            ->willReturn('1234567890');

        $result = $this->modelMock->phone();

        $this->assertIsString($result);
        $this->assertSame('1234567890', $result);
    }

    public function testChangePhoneShouldChangeAndReturnSelf(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('cam_phone', '12345')
            ->willReturnSelf();

        $result = $this->modelMock->changePhone('12345');

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function testChangePhoneShouldChangeAndReturnSelfWhenValueIsNull(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('cam_phone', null)
            ->willReturnSelf();

        $result = $this->modelMock->changePhone();

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function testEmailShouldReturnNull(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('cam_email')
            ->willReturn(null);

        $result = $this->modelMock->email();

        $this->assertNull($result);
    }

    public function testEmailShouldReturnString(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('cam_email')
            ->willReturn('test@test.com');

        $result = $this->modelMock->email();

        $this->assertIsString($result);
        $this->assertSame('test@test.com', $result);
    }

    public function testChangeEmailShouldChangeAndReturnSelf(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('cam_email', 'test@test.com')
            ->willReturnSelf();

        $result = $this->modelMock->changeEmail('test@test.com');

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function testChangeEmailShouldChangeAndReturnSelfWhenValueIsNull(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('cam_email', null)
            ->willReturnSelf();

        $result = $this->modelMock->changeEmail();

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function testObservationsShouldReturnNull(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('cam_observations')
            ->willReturn(null);

        $result = $this->modelMock->observations();

        $this->assertNull($result);
    }

    public function testObservationsShouldReturnString(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('cam_observations')
            ->willReturn('hello world');

        $result = $this->modelMock->observations();

        $this->assertIsString($result);
        $this->assertSame('hello world', $result);
    }

    public function testChangeObservationsShouldChangeAndReturnSelf(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('cam_observations', 'hello world')
            ->willReturnSelf();

        $result = $this->modelMock->changeObservations('hello world');

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function testChangeObservationsShouldChangeAndReturnSelfWhenValueIsNull(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('cam_observations', null)
            ->willReturnSelf();

        $result = $this->modelMock->changeObservations();

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function testSearchShouldReturnNull(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('cam_search')
            ->willReturn(null);

        $result = $this->modelMock->search();

        $this->assertNull($result);
    }

    public function testSearchShouldReturnString(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('cam_search')
            ->willReturn('hello world');

        $result = $this->modelMock->search();

        $this->assertIsString($result);
        $this->assertSame('hello world', $result);
    }

    public function testChangeSearchShouldChangeAndReturnSelf(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('cam_search', 'hello world')
            ->willReturnSelf();

        $result = $this->modelMock->changeSearch('hello world');

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function testChangeSearchShouldChangeAndReturnSelfWhenValueIsNull(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('cam_search', null)
            ->willReturnSelf();

        $result = $this->modelMock->changeSearch();

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    public function testStateShouldReturnInt(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('cam_state')
            ->willReturn(1);

        $result = $this->modelMock->state();

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function testChangeStateShouldChangeAndReturnSelf(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('cam_state', 2)
            ->willReturnSelf();

        $result = $this->modelMock->changeState(2);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    /**
     * @throws \Exception
     */
    public function testCreatedAtShouldReturnString(): void
    {
        $datetime = '2024-07-13 07:39:00';
        $this->modelMock = $this->getMockBuilder(Campus::class)
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
    public function testCreatedAtShouldReturnNull(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('created_at')
            ->willReturn(null);

        $result = $this->modelMock->createdAt();

        $this->assertNull($result);
    }

    public function testChangeCreatedAtShouldChangeAndReturnSelf(): void
    {
        $datetime = new \DateTime();
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('created_at', $datetime)
            ->willReturnSelf();

        $result = $this->modelMock->changeCreatedAt($datetime);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    /**
     * @throws \Exception
     */
    public function testUpdatedAtShouldReturnString(): void
    {
        $datetime = '2024-07-13 07:39:00';
        $this->modelMock = $this->getMockBuilder(Campus::class)
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
    public function testUpdatedAtShouldReturnNull(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('updated_at')
            ->willReturn(null);

        $result = $this->modelMock->updatedAt();

        $this->assertNull($result);
    }

    public function testChangeUpdatedAtShouldChangeAndReturnSelf(): void
    {
        $datetime = new \DateTime();
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('updated_at', $datetime)
            ->willReturnSelf();

        $result = $this->modelMock->changeUpdatedAt($datetime);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    /**
     * @throws \Exception
     */
    public function testDeletedAtShouldReturnNull(): void
    {
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('deleted_at')
            ->willReturn(null);

        $result = $this->modelMock->deletedAt();

        $this->assertNull($result);
    }

    public function testChangeDeletedAtShouldChangeAndReturnSelf(): void
    {
        $datetime = new \DateTime();
        $this->modelMock = $this->getMockBuilder(Campus::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('deleted_at', $datetime)
            ->willReturnSelf();

        $result = $this->modelMock->changeDeletedAt($datetime);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCastsShouldReturnArrayWithCasts(): void
    {
        $reflection = new \ReflectionClass(Campus::class);
        $method = $reflection->getMethod('casts');
        $this->assertTrue($method->isProtected());

        $result = $method->invokeArgs($this->model, []);

        $dataExpected = [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];

        $this->assertIsArray($result);
        $this->assertEquals($dataExpected, $result);
    }
}
