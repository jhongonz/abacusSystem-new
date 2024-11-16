<?php

namespace Tests\Feature\Core\Institution\Infrastructure\Persistence\Eloquent\Model;

use Core\Institution\Infrastructure\Persistence\Eloquent\Model\Institution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(Institution::class)]
class InstitutionTest extends TestCase
{
    private Institution $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new Institution();
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
        $this->assertSame('inst_search', $result);
    }

    public function testRelationWithCampusShouldReturnHasMany(): void
    {
        $result = $this->model->relationWithCampus();

        $this->assertInstanceOf(HasMany::class, $result);
    }

    public function testCampusShouldReturnModel(): void
    {
        $result = $this->model->campus();

        $this->assertInstanceOf(Model::class, $result);
        $this->assertSame('campus', $result->getTable());
    }

    public function testIdShouldReturnNull(): void
    {
        $result = $this->model->id();
        $this->assertNull($result);
    }

    public function testIdShouldChangeAndReturnInt(): void
    {
        $result = $this->model->changeId(1);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertSame(1, $result->id());
    }

    public function testCodeShouldReturnNull(): void
    {
        $result = $this->model->code();
        $this->assertNull($result);
    }

    public function testCodeShouldChangeAndReturnString(): void
    {
        $result = $this->model->changeCode('test');

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertSame('test', $result->code());
    }

    public function testNameShouldReturnNull(): void
    {
        $result = $this->model->name();
        $this->assertNull($result);
    }

    public function testNameShouldChangeAndReturnString(): void
    {
        $result = $this->model->changeName('test');

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertSame('test', $result->name());
    }

    public function testShortnameShouldReturnNull(): void
    {
        $result = $this->model->shortname();
        $this->assertNull($result);
    }

    public function testShortnameShouldChangeAndReturnString(): void
    {
        $result = $this->model->changeShortname('test');

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertSame('test', $result->shortname());
    }

    public function testLogoShouldReturnNull(): void
    {
        $result = $this->model->logo();
        $this->assertNull($result);
    }

    public function testLogoShouldChangeAndReturnString(): void
    {
        $result = $this->model->changeLogo('test');

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertSame('test', $result->logo());
    }

    public function testObservationsShouldReturnNull(): void
    {
        $result = $this->model->observations();
        $this->assertNull($result);
    }

    public function testObservationsShouldChangeAndReturnString(): void
    {
        $result = $this->model->changeObservations('test');

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertSame('test', $result->observations());
    }

    public function testAddressShouldReturnNull(): void
    {
        $result = $this->model->address();
        $this->assertNull($result);
    }

    public function testAddressShouldChangeAndReturnString(): void
    {
        $result = $this->model->changeAddress('test');

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertSame('test', $result->address());
    }

    public function testPhoneShouldReturnNull(): void
    {
        $result = $this->model->phone();
        $this->assertNull($result);
    }

    public function testPhoneShouldChangeAndReturnString(): void
    {
        $result = $this->model->changePhone('test');

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertSame('test', $result->phone());
    }

    public function testEmailShouldReturnNull(): void
    {
        $result = $this->model->email();
        $this->assertNull($result);
    }

    public function testEmailShouldChangeAndReturnString(): void
    {
        $result = $this->model->changeEmail('test');

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertSame('test', $result->email());
    }

    public function testStateShouldReturnInt(): void
    {
        $result = $this->model->state();
        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function testStateShouldChangeAndReturnString(): void
    {
        $result = $this->model->changeState(2);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertSame(2, $result->state());
    }

    /**
     * @throws \Exception
     */
    public function testCreatedAtShouldReturnNull(): void
    {
        $result = $this->model->createdAt();
        $this->assertNull($result);
    }

    /**
     * @throws \Exception
     */
    public function testCreatedAtShouldChangeAndReturnDatetime(): void
    {
        $datetime = new \DateTime('2024-05-19 22:09:00');
        $result = $this->model->changeCreatedAt($datetime);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertInstanceOf(\DateTime::class, $result->createdAt());
    }

    /**
     * @throws \Exception
     */
    public function testUpdatedAtShouldReturnNull(): void
    {
        $result = $this->model->updatedAt();
        $this->assertNull($result);
    }

    /**
     * @throws \Exception
     */
    public function testUpdatedAtShouldChangeAndReturnString(): void
    {
        $datetime = new \DateTime('2024-05-19 22:09:00');
        $result = $this->model->changeUpdatedAt($datetime);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertInstanceOf(\DateTime::class, $result->updatedAt());
    }

    public function testSearchShouldReturnNull(): void
    {
        $result = $this->model->search();
        $this->assertNull($result);
    }

    public function testSearchShouldChangeAndReturnString(): void
    {
        $result = $this->model->changeSearch('test');

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertSame('test', $result->search());
    }

    /**
     * @throws \Exception
     */
    public function testDeletedAtShouldReturnNull(): void
    {
        $result = $this->model->deletedAt();
        $this->assertNull($result);
    }

    /**
     * @throws \Exception
     */
    public function testDeletedAtShouldChangeAndReturnString(): void
    {
        $datetime = new \DateTime('2024-05-19 22:09:00');
        $result = $this->model->changeDeletedAt($datetime);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertInstanceOf(\DateTime::class, $result->deletedAt());
    }
}
