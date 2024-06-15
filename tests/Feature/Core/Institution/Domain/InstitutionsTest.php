<?php

namespace Tests\Feature\Core\Institution\Domain;

use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(Institutions::class)]
class InstitutionsTest extends TestCase
{
    private Institutions $institutions;
    private Institution|MockObject $institution;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->institution = $this->createMock(Institution::class);
        $this->institutions = new Institutions($this->institution);
    }

    public function tearDown(): void
    {
        unset($this->institutions, $this->institution);
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_addItem_should_return_self(): void
    {
        $institutionMock = $this->createMock(Institution::class);
        $result = $this->institutions->addItem($institutionMock);

        $this->assertInstanceOf(Institutions::class, $result);
        $this->assertSame($this->institutions, $result);
        $this->assertCount(2, $this->institutions->items());
    }

    public function test_items_should_return_array(): void
    {
        $result = $this->institutions->items();
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_addId_should_return_self(): void
    {
        $result = $this->institutions->addId(1);

        $this->assertInstanceOf(Institutions::class, $result);
        $this->assertSame($this->institutions, $result);
        $this->assertCount(1, $result->aggregator());
    }

    public function test_filters_should_return_array(): void
    {
        $result = $this->institutions->filters();
        $this->assertIsArray($result);
    }

    public function test_setFilters_should_return_self(): void
    {
        $result = $this->institutions->setFilters(['test']);

        $this->assertInstanceOf(Institutions::class, $result);
        $this->assertSame($this->institutions, $result);
        $this->assertSame(['test'], $result->filters());
    }
}
