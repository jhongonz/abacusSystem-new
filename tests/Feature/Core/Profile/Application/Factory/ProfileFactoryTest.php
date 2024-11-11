<?php

namespace Tests\Feature\Core\Profile\Application\Factory;

use Core\Profile\Application\Factory\ProfileFactory;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileCreatedAt;
use Core\Profile\Domain\ValueObjects\ProfileDescription;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileName;
use Core\Profile\Domain\ValueObjects\ProfileSearch;
use Core\Profile\Domain\ValueObjects\ProfileState;
use Core\Profile\Domain\ValueObjects\ProfileUpdatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use Tests\Feature\Core\Profile\Application\Factory\DataProvider\DataProviderFactory;
use Tests\TestCase;

#[CoversClass(ProfileFactory::class)]
class ProfileFactoryTest extends TestCase
{
    private ProfileFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new ProfileFactory;
    }

    public function tearDown(): void
    {
        unset($this->factory);
        parent::tearDown();
    }

    /**
     * @param array<string, mixed> $dataObject
     * @return void
     * @throws \Exception
     */
    #[DataProviderExternal(DataProviderFactory::class, 'providerProfile')]
    public function test_buildProfileFromArray_should_return_profile_object(array $dataObject): void
    {
        $result = $this->factory->buildProfileFromArray($dataObject);
        $data = $dataObject[Profile::TYPE];

        $this->assertInstanceOf(ProfileId::class, $result->id());
        $this->assertSame($data['id'], $result->id()->value());

        $this->assertInstanceOf(ProfileName::class, $result->name());
        $this->assertSame($data['name'], $result->name()->value());

        $this->assertInstanceOf(ProfileState::class, $result->state());
        $this->assertSame($data['state'], $result->state()->value());

        $this->assertInstanceOf(ProfileCreatedAt::class, $result->createdAt());

        $this->assertInstanceOf(ProfileDescription::class, $result->description());
        $this->assertSame($data['description'], $result->description()->value());

        $this->assertIsArray($result->modulesAggregator());
        $this->assertSame($data['modulesAggregator'], $result->modulesAggregator());

        $this->assertInstanceOf(ProfileUpdatedAt::class, $result->updatedAt());

        $this->assertInstanceOf(Profile::class, $result);
    }

    public function test_buildProfileUpdateAt_should_return_value_object_with_null(): void
    {
        $result = $this->factory->buildProfileUpdateAt();

        $this->assertInstanceOf(ProfileUpdatedAt::class, $result);
        $this->assertNull($result->value());
    }

    public function test_buildProfileUpdateAt_should_return_value_object(): void
    {
        $datetime = new \DateTime;
        $result = $this->factory->buildProfileUpdateAt($datetime);

        $this->assertInstanceOf(ProfileUpdatedAt::class, $result);
        $this->assertSame($datetime, $result->value());
    }

    public function test_buildProfileCreatedAt_should_return_value_object(): void
    {
        $datetime = new \DateTime;
        $result = $this->factory->buildProfileCreatedAt($datetime);

        $this->assertInstanceOf(ProfileCreatedAt::class, $result);
        $this->assertSame($datetime, $result->value());
    }

    public function test_buildProfileSearch_should_return_value_object_with_null(): void
    {
        $result = $this->factory->buildProfileSearch();

        $this->assertInstanceOf(ProfileSearch::class, $result);
        $this->assertNull($result->value());
    }

    public function test_buildProfileSearch_should_return_value_object(): void
    {
        $search = 'test test';
        $result = $this->factory->buildProfileSearch($search);

        $this->assertInstanceOf(ProfileSearch::class, $result);
        $this->assertSame($search, $result->value());
    }

    /**
     * @throws Exception
     */
    public function test_buildProfiles_should_return_value_object(): void
    {
        $profileMock = $this->createMock(Profile::class);
        $result = $this->factory->buildProfiles($profileMock);

        $this->assertInstanceOf(Profiles::class, $result);
        $this->assertSame([$profileMock], $result->items());
    }
}
