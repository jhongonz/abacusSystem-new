<?php

namespace Tests\Feature\Core\Institution\Domain;

use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Core\SharedContext\Model\ArrayIterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(ArrayIterator::class)]
#[CoversClass(Institutions::class)]
class InstitutionsTest extends TestCase
{
    private Institutions $institutions;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        unset($this->institutions);
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testAddItemShouldReturnSelf(): void
    {
        $this->institutions = new Institutions();

        $institutionMock = $this->createMock(Institution::class);
        $result = $this->institutions->addItem($institutionMock);

        $this->assertInstanceOf(Institutions::class, $result);
        $this->assertSame($this->institutions, $result);
        $this->assertSame([$institutionMock], $this->institutions->items());
    }

    public function testConstructShouldReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Item is not valid to collection Core\Institution\Domain\Institutions');

        $this->institutions = new Institutions(['testing']);
    }

    /**
     * @throws Exception
     */
    public function testItemsShouldReturnArray(): void
    {
        $institutionMock = $this->createMock(Institution::class);

        $this->institutions = new Institutions([$institutionMock]);
        $result = $this->institutions->items();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame([$institutionMock], $result);
    }

    public function testAddIdShouldReturnSelf(): void
    {
        $this->institutions = new Institutions();
        $result = $this->institutions->addId(1);

        $this->assertInstanceOf(Institutions::class, $result);
        $this->assertSame($this->institutions, $result);
        $this->assertSame([1], $result->aggregator());
    }

    public function testFiltersShouldReturnArray(): void
    {
        $this->institutions = new Institutions();

        $result = $this->institutions->filters();
        $this->assertIsArray($result);
    }

    public function testSetFiltersShouldReturnSelf(): void
    {
        $this->institutions = new Institutions();
        $result = $this->institutions->setFilters(['test']);

        $this->assertInstanceOf(Institutions::class, $result);
        $this->assertSame($this->institutions, $result);
        $this->assertSame(['test'], $result->filters());
    }
}
