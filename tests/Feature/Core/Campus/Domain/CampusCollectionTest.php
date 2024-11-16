<?php

namespace Tests\Feature\Core\Campus\Domain;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;
use Core\SharedContext\Model\ArrayIterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CampusCollection::class)]
#[CoversClass(ArrayIterator::class)]
class CampusCollectionTest extends TestCase
{
    private Campus|MockObject $campus;
    private CampusCollection $campusCollection;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->campus = $this->createMock(Campus::class);
        $this->campusCollection = new CampusCollection([$this->campus]);
    }

    public function tearDown(): void
    {
        unset(
            $this->campus,
            $this->campusCollection,
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testAddItemShouldReturnSelf(): void
    {
        $campusMock = $this->createMock(Campus::class);
        $result = $this->campusCollection->addItem($campusMock);

        $this->assertInstanceOf(CampusCollection::class, $result);
        $this->assertSame($this->campusCollection, $result);
    }

    public function testConstructShouldReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Item is not valid to collection Core\Campus\Domain\CampusCollection');

        $this->campusCollection = new CampusCollection(['testing']);
    }

    public function testItemsShouldReturnArray(): void
    {
        $result = $this->campusCollection->items();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function testAddIdShouldReturnSelf(): void
    {
        $result = $this->campusCollection->addId(1);

        $this->assertInstanceOf(CampusCollection::class, $result);
        $this->assertSame($this->campusCollection, $result);
    }

    public function testAggregatorShouldReturnArray(): void
    {
        $result = $this->campusCollection->aggregator();
        $this->assertIsArray($result);
    }

    public function testFiltersShouldReturnArray(): void
    {
        $result = $this->campusCollection->filters();
        $this->assertIsArray($result);
    }

    public function testSetFiltersShouldReturnSelf(): void
    {
        $result = $this->campusCollection->setFilters(['testing']);

        $this->assertInstanceOf(CampusCollection::class, $result);
        $this->assertSame($this->campusCollection, $result);
        $this->assertSame(['testing'], $result->filters());
    }
}
