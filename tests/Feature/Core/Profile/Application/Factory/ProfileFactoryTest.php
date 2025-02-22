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
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Feature\Core\Profile\Application\Factory\DataProvider\DataProviderFactory;
use Tests\TestCase;

#[CoversClass(ProfileFactory::class)]
class ProfileFactoryTest extends TestCase
{
    private ProfileFactory|MockObject $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new ProfileFactory();
    }

    public function tearDown(): void
    {
        unset($this->factory);
        parent::tearDown();
    }

    /**
     * @param array<string, mixed> $dataObject
     *
     * @throws \Exception
     * @throws Exception
     */
    #[DataProviderExternal(DataProviderFactory::class, 'providerProfile')]
    public function testBuildProfileFromArrayShouldReturnProfileObject(array $dataObject): void
    {
        $dataProvider = $dataObject[Profile::TYPE];
        $this->factory = $this->getMockBuilder(ProfileFactory::class)
            ->onlyMethods([
                'buildProfile',
                'buildProfileId',
                'buildProfileName',
            ])
            ->getMock();

        $idMock = $this->createMock(ProfileId::class);
        $this->factory->expects(self::once())
            ->method('buildProfileId')
            ->with($dataProvider['id'])
            ->willReturn($idMock);

        $nameMock = $this->createMock(ProfileName::class);
        $this->factory->expects(self::once())
            ->method('buildProfileName')
            ->with($dataProvider['name'])
            ->willReturn($nameMock);

        $profileMock = $this->createMock(Profile::class);

        $descriptionMock = $this->createMock(ProfileDescription::class);
        $descriptionMock->expects(self::once())
            ->method('setValue')
            ->with($dataProvider['description'])
            ->willReturnSelf();
        $profileMock->expects(self::once())
            ->method('description')
            ->willReturn($descriptionMock);

        $stateMock = $this->createMock(ProfileState::class);
        $stateMock->expects(self::once())
            ->method('setValue')
            ->with($dataProvider['state'])
            ->willReturnSelf();
        $profileMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $profileMock->expects(self::once())
            ->method('setModulesAggregator')
            ->with($dataProvider['modulesAggregator'])
            ->willReturnSelf();

        $createdAtMock = $this->createMock(ProfileCreatedAt::class);
        $createdAtMock->expects(self::once())
            ->method('setValue')
            ->with(new \DateTime($dataProvider['createdAt']))
            ->willReturnSelf();
        $profileMock->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAtMock);

        $updatedAtMock = $this->createMock(ProfileUpdatedAt::class);
        $updatedAtMock->expects(self::once())
            ->method('setValue')
            ->with(new \DateTime($dataProvider['updatedAt']))
            ->willReturnSelf();
        $profileMock->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updatedAtMock);

        $this->factory->expects(self::once())
            ->method('buildProfile')
            ->withAnyParameters()
            ->willReturn($profileMock);

        $result = $this->factory->buildProfileFromArray($dataObject);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profileMock, $result);
    }

    /**
     * @throws Exception
     */
    public function testBuildProfileShouldReturnObject(): void
    {
        $profileIdMock = $this->createMock(ProfileId::class);
        $profileNameMock = $this->createMock(ProfileName::class);
        $profileStateMock = $this->createMock(ProfileState::class);
        $profileCreatedAtMock = $this->createMock(ProfileCreatedAt::class);

        $result = $this->factory->buildProfile(
            $profileIdMock,
            $profileNameMock,
            $profileStateMock,
            $profileCreatedAtMock
        );

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profileIdMock, $result->id());
        $this->assertSame($profileNameMock, $result->name());
        $this->assertSame($profileStateMock, $result->state());
        $this->assertSame($profileCreatedAtMock, $result->createdAt());
    }

    public function testBuildProfileIdShouldReturnObject(): void
    {
        $result = $this->factory->buildProfileId(10);

        $this->assertInstanceOf(ProfileId::class, $result);
        $this->assertSame(10, $result->value());
    }

    public function testBuildProfileIdShouldReturnObjectWithNull(): void
    {
        $result = $this->factory->buildProfileId();

        $this->assertInstanceOf(ProfileId::class, $result);
        $this->assertNull($result->value());
    }

    public function testBuildProfileNameShouldReturnObject(): void
    {
        $result = $this->factory->buildProfileName('name');

        $this->assertInstanceOf(ProfileName::class, $result);
        $this->assertSame('name', $result->value());
    }

    /**
     * @throws \Exception
     */
    public function testBuildProfileStateShouldReturnObject(): void
    {
        $result = $this->factory->buildProfileState();

        $this->assertInstanceOf(ProfileState::class, $result);
        $this->assertSame(1, $result->value());
    }

    /**
     * @throws \Exception
     */
    public function testBuildProfileStateShouldReturnObjectWithState(): void
    {
        $result = $this->factory->buildProfileState(2);

        $this->assertInstanceOf(ProfileState::class, $result);
        $this->assertSame(2, $result->value());
    }

    public function testBuildProfileUpdateAtShouldReturnValueObjectWithNull(): void
    {
        $result = $this->factory->buildProfileUpdateAt();

        $this->assertInstanceOf(ProfileUpdatedAt::class, $result);
        $this->assertNull($result->value());
    }

    public function testBuildProfileUpdateAtShouldReturnValueObject(): void
    {
        $datetime = new \DateTime();
        $result = $this->factory->buildProfileUpdateAt($datetime);

        $this->assertInstanceOf(ProfileUpdatedAt::class, $result);
        $this->assertSame($datetime, $result->value());
    }

    public function testBuildProfileCreatedAtShouldReturnValueObject(): void
    {
        $datetime = new \DateTime();
        $result = $this->factory->buildProfileCreatedAt($datetime);

        $this->assertInstanceOf(ProfileCreatedAt::class, $result);
        $this->assertSame($datetime, $result->value());
    }

    public function testBuildProfileSearchShouldReturnValueObjectWithNull(): void
    {
        $result = $this->factory->buildProfileSearch();

        $this->assertInstanceOf(ProfileSearch::class, $result);
        $this->assertNull($result->value());
    }

    public function testBuildProfileSearchShouldReturnValueObject(): void
    {
        $search = 'test test';
        $result = $this->factory->buildProfileSearch($search);

        $this->assertInstanceOf(ProfileSearch::class, $result);
        $this->assertSame($search, $result->value());
    }

    public function testBuildProfileDescriptionShouldReturnValueObject(): void
    {
        $result = $this->factory->buildProfileDescription('test');

        $this->assertInstanceOf(ProfileDescription::class, $result);
        $this->assertSame('test', $result->value());
    }

    public function testBuildProfileDescriptionShouldReturnValueObjectWithNull(): void
    {
        $result = $this->factory->buildProfileDescription();

        $this->assertInstanceOf(ProfileDescription::class, $result);
        $this->assertNull($result->value());
    }

    /**
     * @throws Exception
     */
    public function testBuildProfilesShouldReturnValueObject(): void
    {
        $profileMock = $this->createMock(Profile::class);
        $result = $this->factory->buildProfiles($profileMock);

        $this->assertInstanceOf(Profiles::class, $result);
        $this->assertSame([$profileMock], $result->items());
    }
}
