<?php

namespace Tests\Feature\Core\Profile\Application\DataTransformer;

use Core\Profile\Application\DataTransformer\ProfileDataTransformer;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\ValueObjects\ProfileCreatedAt;
use Core\Profile\Domain\ValueObjects\ProfileDescription;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileName;
use Core\Profile\Domain\ValueObjects\ProfileState;
use Core\Profile\Domain\ValueObjects\ProfileUpdatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Feature\Core\Profile\Application\DataTransformer\DataProvider\DataProviderDataTransformer;
use Tests\TestCase;

#[CoversClass(ProfileDataTransformer::class)]
class ProfileDataTransformerTest extends TestCase
{
    private Profile|MockObject $profile;

    private ProfileDataTransformer $dataTransformer;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->profile = $this->createMock(Profile::class);
        $this->dataTransformer = new ProfileDataTransformer();
    }

    public function tearDown(): void
    {
        unset(
            $this->profile,
            $this->dataTransformer
        );
        parent::tearDown();
    }

    public function test_write_should_return_self(): void
    {
        $result = $this->dataTransformer->write($this->profile);

        $this->assertInstanceOf(ProfileDataTransformer::class, $result);
        $this->assertSame($result, $this->dataTransformer);
    }

    /**
     * @throws Exception
     */
    #[DataProviderExternal(DataProviderDataTransformer::class, 'providerProfileToRead')]
    public function test_read_should_return_array(array $expected, \DateTime $datetime): void
    {
        $profileId = $this->createMock(ProfileId::class);
        $profileId->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $this->profile->expects(self::once())
            ->method('id')
            ->willReturn($profileId);

        $profileName = $this->createMock(ProfileName::class);
        $profileName->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $this->profile->expects(self::once())
            ->method('name')
            ->willReturn($profileName);

        $profileDescription = $this->createMock(ProfileDescription::class);
        $profileDescription->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $this->profile->expects(self::once())
            ->method('description')
            ->willReturn($profileDescription);

        $profileState = $this->createMock(ProfileState::class);
        $profileState->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $this->profile->expects(self::once())
            ->method('state')
            ->willReturn($profileState);

        $createdAt = $this->createMock(ProfileCreatedAt::class);
        $createdAt->expects(self::once())
            ->method('value')
            ->willReturn($datetime);
        $this->profile->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAt);

        $updateAt = $this->createMock(ProfileUpdatedAt::class);
        $updateAt->expects(self::once())
            ->method('value')
            ->willReturn($datetime);
        $this->profile->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updateAt);

        $this->dataTransformer->write($this->profile);
        $result = $this->dataTransformer->read();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('profile', $result);
        $this->assertSame($expected, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    #[DataProviderExternal(DataProviderDataTransformer::class, 'providerProfileToReadToShare')]
    public function test_readToShare_should_return_array(array $expected, \DateTime $datetime): void
    {
        $profileId = $this->createMock(ProfileId::class);
        $profileId->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $this->profile->expects(self::once())
            ->method('id')
            ->willReturn($profileId);

        $profileName = $this->createMock(ProfileName::class);
        $profileName->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $this->profile->expects(self::once())
            ->method('name')
            ->willReturn($profileName);

        $profileDescription = $this->createMock(ProfileDescription::class);
        $profileDescription->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $this->profile->expects(self::once())
            ->method('description')
            ->willReturn($profileDescription);

        $profileState = $this->createMock(ProfileState::class);
        $profileState->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $profileState->expects(self::once())
            ->method('formatHtmlToState')
            ->willReturn('test');

        $this->profile->expects(self::exactly(2))
            ->method('state')
            ->willReturn($profileState);

        $createdAt = $this->createMock(ProfileCreatedAt::class);
        $createdAt->expects(self::once())
            ->method('value')
            ->willReturn($datetime);
        $this->profile->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAt);

        $updateAt = $this->createMock(ProfileUpdatedAt::class);
        $updateAt->expects(self::once())
            ->method('value')
            ->willReturn($datetime);
        $this->profile->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updateAt);

        $this->dataTransformer->write($this->profile);
        $result = $this->dataTransformer->readToShare();

        $this->assertIsArray($result);
        $this->assertArrayNotHasKey('profile', $result);
        $this->assertSame($expected, $result);
    }
}
