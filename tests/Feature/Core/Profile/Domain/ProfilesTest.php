<?php

namespace Tests\Feature\Core\Profile\Domain;

use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(Profiles::class)]
class ProfilesTest extends TestCase
{
    private Profile|MockObject $profile;

    private Profiles $profiles;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->profile = $this->createMock(Profile::class);
        $this->profiles = new Profiles([$this->profile]);
    }

    public function tearDown(): void
    {
        unset($this->profiles, $this->profile);
        parent::tearDown();
    }

    public function testConstructShouldReturnExceptionWhenIsNotValid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Item is not valid to collection Core\Profile\Domain\Profiles');

        $this->profiles = new Profiles(['testing']);
    }

    /**
     * @throws Exception
     */
    public function testAddItemShouldReturnSelf(): void
    {
        $profile = $this->createMock(Profile::class);
        $result = $this->profiles->addItem($profile);

        $this->assertInstanceOf(Profiles::class, $result);
        $this->assertSame($result, $this->profiles);
        $this->assertCount(2, $result->items());
        $this->assertSame([$this->profile, $profile], $result->items());
    }

    public function testItemsShouldReturnArray(): void
    {
        $result = $this->profiles->items();

        $this->assertIsArray($result);
        $this->assertSame([$this->profile], $result);
    }

    public function testFiltersShouldReturnArray(): void
    {
        $result = $this->profiles->filters();
        $this->assertIsArray($result);
    }

    public function testSetFiltersShouldReturnSelf(): void
    {
        $filters = ['test'];
        $result = $this->profiles->setFilters($filters);

        $this->assertInstanceOf(Profiles::class, $result);
        $this->assertSame($result, $this->profiles);
        $this->assertSame($filters, $result->filters());
    }

    public function testAddIdShouldReturnSelf(): void
    {
        $result = $this->profiles->addId(1);

        $this->assertInstanceOf(Profiles::class, $result);
        $this->assertSame($result, $this->profiles);
        $this->assertSame([1], $result->aggregator());
    }

    public function testAggregatorShouldReturnArray(): void
    {
        $result = $this->profiles->aggregator();
        $this->assertIsArray($result);
    }
}
