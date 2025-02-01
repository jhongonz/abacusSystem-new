<?php

namespace Tests\Feature\Core\Campus\Domain;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;
use Core\SharedContext\Model\ArrayIterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

#[CoversClass(CampusCollection::class)]
#[CoversClass(ArrayIterator::class)]
class CampusCollectionTest extends TestCase
{
    private CampusCollection $campusCollection;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        unset($this->campusCollection);
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testAddItemShouldReturnSelf(): void
    {
        $campusMock1 = $this->createMock(Campus::class);
        $this->campusCollection = new CampusCollection([$campusMock1]);

        $campusMock2 = $this->createMock(Campus::class);
        $this->campusCollection->addItem($campusMock2);

        $this->assertSame($this->campusCollection, $this->campusCollection);
        $this->assertEquals([$campusMock1, $campusMock2], $this->campusCollection->items());
    }

    public function testConstructShouldReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Item is not valid to collection Core\Campus\Domain\CampusCollection');

        $this->campusCollection = new CampusCollection(['testing']);
    }

    /**
     * @throws Exception
     */
    public function testItemsShouldReturnArray(): void
    {
        $campusMock = $this->createMock(Campus::class);
        $this->campusCollection = new CampusCollection([$campusMock]);
        $result = $this->campusCollection->items();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals([$campusMock], $result);
    }

    public function testAddIdShouldReturnSelf(): void
    {
        $this->campusCollection = new CampusCollection();
        $result = $this->campusCollection->addId(1);

        $this->assertInstanceOf(CampusCollection::class, $result);
        $this->assertSame($this->campusCollection, $result);
    }

    /**
     * @throws Exception
     */
    public function testAggregatorShouldReturnArray(): void
    {
        $campusMock = $this->createMock(Campus::class);
        $this->campusCollection = new CampusCollection([$campusMock]);

        $result = $this->campusCollection->aggregator();
        $this->assertIsArray($result);
    }

    public function testFiltersShouldReturnArray(): void
    {
        $this->campusCollection = new CampusCollection();
        $result = $this->campusCollection->filters();

        $this->assertIsArray($result);
    }

    public function testSetFiltersShouldReturnSelf(): void
    {
        $this->campusCollection = new CampusCollection();
        $result = $this->campusCollection->setFilters(['testing']);

        $this->assertInstanceOf(CampusCollection::class, $result);
        $this->assertSame($this->campusCollection, $result);
        $this->assertSame(['testing'], $result->filters());
    }
}
