<?php

namespace Tests\Feature\Core\Institution\Infrastructure\Persistence\Eloquent\Model;

use Core\Institution\Infrastructure\Persistence\Eloquent\Model\Institution;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(Institution::class)]
class InstitutionTest extends TestCase
{
    private Institution $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new Institution;
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
        $this->assertSame('inst_search', $result);
    }

    public function test_id_should_return_null(): void
    {
        $result = $this->model->id();
        $this->assertNull($result);
    }

    public function test_id_should_change_and_return_int(): void
    {
        $result = $this->model->changeId(1);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertSame(1, $result->id());
    }

    public function test_code_should_return_null(): void
    {
        $result = $this->model->code();
        $this->assertNull($result);
    }

    public function test_code_should_change_and_return_string(): void
    {
        $result = $this->model->changeCode('test');

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertSame('test', $result->code());
    }

    public function test_name_should_return_null(): void
    {
        $result = $this->model->name();
        $this->assertNull($result);
    }

    public function test_name_should_change_and_return_string(): void
    {
        $result = $this->model->changeName('test');

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertSame('test', $result->name());
    }

    public function test_shortname_should_return_null(): void
    {
        $result = $this->model->shortname();
        $this->assertNull($result);
    }

    public function test_shortname_should_change_and_return_string(): void
    {
        $result = $this->model->changeShortname('test');

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertSame('test', $result->shortname());
    }

    public function test_logo_should_return_null(): void
    {
        $result = $this->model->logo();
        $this->assertNull($result);
    }

    public function test_logo_should_change_and_return_string(): void
    {
        $result = $this->model->changeLogo('test');

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertSame('test', $result->logo());
    }

    public function test_observations_should_return_null(): void
    {
        $result = $this->model->observations();
        $this->assertNull($result);
    }

    public function test_observations_should_change_and_return_string(): void
    {
        $result = $this->model->changeObservations('test');

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertSame('test', $result->observations());
    }

    public function test_state_should_return_int(): void
    {
        $result = $this->model->state();
        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function test_state_should_change_and_return_string(): void
    {
        $result = $this->model->changeState(2);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertSame(2, $result->state());
    }

    /**
     * @throws Exception
     */
    public function test_createdAt_should_return_null(): void
    {
        $result = $this->model->createdAt();
        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function test_createdAt_should_change_and_return_datetime(): void
    {
        $datetime = new \DateTime('2024-05-19 22:09:00');
        $result = $this->model->changeCreatedAt($datetime);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertInstanceOf(\DateTime::class, $result->createdAt());
    }

    /**
     * @throws Exception
     */
    public function test_updatedAt_should_return_null(): void
    {
        $result = $this->model->updatedAt();
        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function test_updatedAt_should_change_and_return_string(): void
    {
        $datetime = new \DateTime('2024-05-19 22:09:00');
        $result = $this->model->changeUpdatedAt($datetime);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertInstanceOf(\DateTime::class, $result->updatedAt());
    }

    public function test_search_should_return_null(): void
    {
        $result = $this->model->search();
        $this->assertNull($result);
    }

    public function test_search_should_change_and_return_string(): void
    {
        $result = $this->model->changeSearch('test');

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertSame('test', $result->search());
    }

    /**
     * @throws Exception
     */
    public function test_deletedAt_should_return_null(): void
    {
        $result = $this->model->deletedAt();
        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function test_deletedAt_should_change_and_return_string(): void
    {
        $datetime = new \DateTime('2024-05-19 22:09:00');
        $result = $this->model->changeDeletedAt($datetime);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->model, $result);
        $this->assertInstanceOf(\DateTime::class, $result->deletedAt());
    }
}
