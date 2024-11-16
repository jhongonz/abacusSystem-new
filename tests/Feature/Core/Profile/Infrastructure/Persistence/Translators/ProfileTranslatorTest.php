<?php

namespace Tests\Feature\Core\Profile\Infrastructure\Persistence\Translators;

use Core\Profile\Domain\Contracts\ProfileFactoryContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileCreatedAt;
use Core\Profile\Domain\ValueObjects\ProfileDescription;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileName;
use Core\Profile\Domain\ValueObjects\ProfileSearch;
use Core\Profile\Domain\ValueObjects\ProfileState;
use Core\Profile\Domain\ValueObjects\ProfileUpdatedAt;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Module;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Profile as ProfileModel;
use Core\Profile\Infrastructure\Persistence\Translators\ProfileTranslator;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(ProfileTranslator::class)]
class ProfileTranslatorTest extends TestCase
{
    private ProfileFactoryContract|MockObject $factory;
    private ProfileModel|MockObject $model;
    private ProfileTranslator $translator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->createMock(ProfileFactoryContract::class);
        $this->translator = new ProfileTranslator($this->factory);
    }

    public function tearDown(): void
    {
        unset(
            $this->model,
            $this->factory,
            $this->translator
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testSetModelShouldReturnSelf(): void
    {
        $model = $this->createMock(ProfileModel::class);
        $return = $this->translator->setModel($model);

        $this->assertInstanceOf(ProfileTranslator::class, $return);
        $this->assertSame($this->translator, $return);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testToDomainShouldReturnObject(): void
    {
        $this->model = $this->createMock(ProfileModel::class);
        $profileMock = $this->createMock(Profile::class);

        $this->model->expects(self::once())
            ->method('id')
            ->willReturn(1);
        $profileId = $this->createMock(ProfileId::class);
        $this->factory->expects(self::once())
            ->method('buildProfileId')
            ->with(1)
            ->willReturn($profileId);

        $this->model->expects(self::once())
            ->method('name')
            ->willReturn('test');
        $name = $this->createMock(ProfileName::class);
        $this->factory->expects(self::once())
            ->method('buildProfileName')
            ->with('test')
            ->willReturn($name);

        $this->model->expects(self::once())
            ->method('state')
            ->willReturn(1);
        $state = $this->createMock(ProfileState::class);
        $this->factory->expects(self::once())
            ->method('buildProfileState')
            ->with(1)
            ->willReturn($state);

        $datetime = new \DateTime('2024-05-14 13:08:00');
        $this->model->expects(self::once())
            ->method('createdAt')
            ->willReturn($datetime);
        $createdAt = $this->createMock(ProfileCreatedAt::class);
        $this->factory->expects(self::once())
            ->method('buildProfileCreatedAt')
            ->with($datetime)
            ->willReturn($createdAt);

        $this->model->expects(self::once())
            ->method('description')
            ->willReturn('test');
        $description = $this->createMock(ProfileDescription::class);
        $this->factory->expects(self::once())
            ->method('buildProfileDescription')
            ->with('test')
            ->willReturn($description);

        $this->model->expects(self::once())
            ->method('search')
            ->willReturn('test');
        $search = $this->createMock(ProfileSearch::class);
        $this->factory->expects(self::once())
            ->method('buildProfileSearch')
            ->with('test')
            ->willReturn($search);

        $this->model->expects(self::once())
            ->method('updatedAt')
            ->willReturn($datetime);
        $updatedAt = $this->createMock(ProfileUpdatedAt::class);
        $this->factory->expects(self::once())
            ->method('buildProfileUpdateAt')
            ->with($datetime)
            ->willReturn($updatedAt);

        $this->factory->expects(self::once())
            ->method('buildProfile')
            ->willReturn($profileMock);

        $modelMock = $this->createMock(Module::class);
        $modelMock->expects(self::once())
            ->method('state')
            ->willReturn(2);
        $modelMock->expects(self::once())
            ->method('id')
            ->willReturn(1);

        $relationMock = $this->mock(BelongsToMany::class);
        $relationMock->shouldReceive('get')
            ->once()
            ->andReturn([$modelMock]);

        $this->model->expects(self::once())
            ->method('pivotModules')
            ->willReturn($relationMock);

        $profileMock->expects(self::once())
            ->method('setModulesAggregator')
            ->with([1])
            ->willReturnSelf();

        $this->translator->setModel($this->model);
        $result = $this->translator->toDomain();

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profileMock, $result);
        $this->assertIsArray($result->modulesAggregator());
    }

    public function testSetCollectionShouldReturnSelf(): void
    {
        $result = $this->translator->setCollection([1]);

        $this->assertInstanceOf(ProfileTranslator::class, $result);
        $this->assertSame($this->translator, $result);
    }

    public function testToDomainCollectionShouldReturnObject(): void
    {
        $this->translator->setCollection([1]);
        $result = $this->translator->toDomainCollection();

        $this->assertInstanceOf(Profiles::class, $result);
        $this->assertIsArray($result->aggregator());
        $this->assertSame([1], $result->aggregator());
        $this->assertCount(1, $result->aggregator());
    }
}
