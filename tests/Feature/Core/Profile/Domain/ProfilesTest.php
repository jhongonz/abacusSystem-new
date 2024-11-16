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
        unset(
            $this->profiles,
            $this->profile
        );
        parent::tearDown();
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
    }

    public function testItemsShouldReturnArray(): void
    {
        $result = $this->profiles->items();
        $this->assertIsArray($result);
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
