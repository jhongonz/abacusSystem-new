<?php

namespace Tests\Feature\Core\Profile\Domain;

use Core\Profile\Domain\Modules;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\ValueObjects\ProfileCreatedAt;
use Core\Profile\Domain\ValueObjects\ProfileDescription;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileName;
use Core\Profile\Domain\ValueObjects\ProfileSearch;
use Core\Profile\Domain\ValueObjects\ProfileState;
use Core\Profile\Domain\ValueObjects\ProfileUpdatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(Profile::class)]
class ProfileTest extends TestCase
{
    private ProfileId|MockObject $profileId;

    private ProfileName|MockObject $profileName;

    private Profile $profile;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->profileId = $this->createMock(ProfileId::class);
        $this->profileName = $this->createMock(ProfileName::class);
        $this->profile = new Profile(
            $this->profileId,
            $this->profileName
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->profile,
            $this->profileId,
            $this->profileName
        );
        parent::tearDown();
    }

    public function testIdShouldReturnValueObject(): void
    {
        $result = $this->profile->id();
        $this->assertInstanceOf(ProfileId::class, $result);
        $this->assertSame($result, $this->profileId);
    }

    /**
     * @throws Exception
     */
    public function testSetIdShouldReturnSelf(): void
    {
        $idMock = $this->createMock(ProfileId::class);
        $result = $this->profile->setId($idMock);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($result, $this->profile);
        $this->assertSame($idMock, $result->id());
    }

    public function testNameShouldReturnValueObject(): void
    {
        $result = $this->profile->name();
        $this->assertInstanceOf(ProfileName::class, $result);
        $this->assertSame($result, $this->profileName);
    }

    /**
     * @throws Exception
     */
    public function testSetNameShouldReturnSelf(): void
    {
        $nameMock = $this->createMock(ProfileName::class);
        $result = $this->profile->setName($nameMock);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($result, $this->profile);
        $this->assertSame($nameMock, $result->name());
    }

    public function testStateShouldReturnValueObject(): void
    {
        $result = $this->profile->state();
        $this->assertInstanceOf(ProfileState::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetStateShouldReturnSelf(): void
    {
        $stateMock = $this->createMock(ProfileState::class);
        $result = $this->profile->setState($stateMock);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($result, $this->profile);
        $this->assertSame($stateMock, $result->state());
    }

    public function testSearchShouldReturnValueObject(): void
    {
        $result = $this->profile->search();
        $this->assertInstanceOf(ProfileSearch::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetSearchShouldReturnSelf(): void
    {
        $searchMock = $this->createMock(ProfileSearch::class);
        $result = $this->profile->setSearch($searchMock);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($result, $this->profile);
        $this->assertSame($searchMock, $result->search());
    }

    public function testDescriptionShouldReturnValueObject(): void
    {
        $result = $this->profile->description();
        $this->assertInstanceOf(ProfileDescription::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetDescriptionShouldReturnSelf(): void
    {
        $descriptionMock = $this->createMock(ProfileDescription::class);
        $result = $this->profile->setDescription($descriptionMock);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($result, $this->profile);
        $this->assertSame($descriptionMock, $result->description());
    }

    public function testModulesShouldReturnValueObject(): void
    {
        $result = $this->profile->modules();
        $this->assertInstanceOf(Modules::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetModulesShouldReturnSelf(): void
    {
        $modulesMock = $this->createMock(Modules::class);
        $result = $this->profile->setModules($modulesMock);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($result, $this->profile);
        $this->assertSame($modulesMock, $result->modules());
    }

    public function testModulesAggregatorShouldReturnArray(): void
    {
        $result = $this->profile->modulesAggregator();
        $this->assertIsArray($result);
    }

    public function testSetModulesAggregatorShouldReturnSelf(): void
    {
        $modulesMock = ['hello'];
        $result = $this->profile->setModulesAggregator($modulesMock);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($result, $this->profile);
        $this->assertSame($modulesMock, $result->modulesAggregator());
    }

    public function testCreatedAtShouldReturnValueObject(): void
    {
        $result = $this->profile->createdAt();
        $this->assertInstanceOf(ProfileCreatedAt::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetCreatedAtShouldReturnSelf(): void
    {
        $createdAt = $this->createMock(ProfileCreatedAt::class);
        $result = $this->profile->setCreatedAt($createdAt);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($result, $this->profile);
        $this->assertSame($createdAt, $result->createdAt());
    }

    public function testUpdatedAtShouldReturnValueObject(): void
    {
        $result = $this->profile->updatedAt();
        $this->assertInstanceOf(ProfileUpdatedAt::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetUpdatedAtShouldReturnSelf(): void
    {
        $updatedAt = $this->createMock(ProfileUpdatedAt::class);
        $result = $this->profile->setUpdatedAt($updatedAt);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($result, $this->profile);
        $this->assertSame($updatedAt, $result->updatedAt());
    }

    /**
     * @throws Exception
     */
    public function testRefreshSearchShouldReturnSelf(): void
    {
        $this->profileName->expects(self::once())
            ->method('value')
            ->willReturn('tesT ');

        $description = $this->createMock(ProfileDescription::class);
        $description->expects(self::once())
            ->method('value')
            ->willReturn('  TEst ');
        $this->profile->setDescription($description);

        $search = $this->createMock(ProfileSearch::class);
        $search->expects(self::once())
            ->method('setValue')
            ->with('test test')
            ->willReturnSelf();
        $this->profile->setSearch($search);

        $result = $this->profile->refreshSearch();
        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($result, $this->profile);
    }
}
