<?php

namespace Tests\Feature\Core\Campus\Application\UseCases\UpdateCampus;

use Core\Campus\Application\UseCases\CreateCampus\CreateCampusRequest;
use Core\Campus\Application\UseCases\UpdateCampus\UpdateCampus;
use Core\Campus\Application\UseCases\UpdateCampus\UpdateCampusRequest;
use Core\Campus\Application\UseCases\UseCasesService;
use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusRepositoryContract;
use Core\Campus\Domain\ValueObjects\CampusAddress;
use Core\Campus\Domain\ValueObjects\CampusCreatedAt;
use Core\Campus\Domain\ValueObjects\CampusEmail;
use Core\Campus\Domain\ValueObjects\CampusId;
use Core\Campus\Domain\ValueObjects\CampusInstitutionId;
use Core\Campus\Domain\ValueObjects\CampusName;
use Core\Campus\Domain\ValueObjects\CampusObservations;
use Core\Campus\Domain\ValueObjects\CampusPhone;
use Core\Campus\Domain\ValueObjects\CampusSearch;
use Core\Campus\Domain\ValueObjects\CampusState;
use Core\Campus\Domain\ValueObjects\CampusUpdatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Feature\Core\Campus\Application\DataProvider\UpdateCampusDataProvider;
use Tests\TestCase;

#[CoversClass(UpdateCampus::class)]
#[CoversClass(UseCasesService::class)]
class UpdateCampusTest extends TestCase
{
    private CampusRepositoryContract|MockObject $campusRepository;
    private UpdateCampus|MockObject $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->campusRepository = $this->createMock(CampusRepositoryContract::class);
        $this->useCase = $this->getMockBuilder(UpdateCampus::class)
            ->setConstructorArgs([$this->campusRepository])
            ->onlyMethods(['getFunctionName'])
            ->getMock();
    }

    public function tearDown(): void
    {
        unset(
            $this->campusRepository,
            $this->useCase
        );
        parent::tearDown();
    }

    /**
     * @param array<string|int, mixed> $dataTest
     *
     * @throws Exception
     * @throws \Exception
     */
    #[DataProviderExternal(UpdateCampusDataProvider::class, 'provider_update_campus')]
    public function testExecuteShouldReturnObject(array $dataTest): void
    {
        $requestMock = $this->createMock(UpdateCampusRequest::class);

        $campusId = $this->createMock(CampusId::class);
        $requestMock->expects(self::once())
            ->method('id')
            ->willReturn($campusId);

        $requestMock->expects(self::once())
            ->method('data')
            ->willReturn($dataTest);

        $this->useCase->expects(self::exactly(10))
            ->method('getFunctionName')
            ->willReturnCallback(function (string $field) {
                return match ($field) {
                    'institutionId' => 'changeInstitutionId',
                    'name' => 'changeName',
                    'address' => 'changeAddress',
                    'phone' => 'changePhone',
                    'email' => 'changeEmail',
                    'observations' => 'changeObservations',
                    'search' => 'changeSearch',
                    'state' => 'changeState',
                    'createdAt' => 'changeCreatedAt',
                    'updatedAt' => 'changeUpdatedAt',
                    default => null,
                };
            });

        $campusMock = $this->createMock(Campus::class);

        $institutionIdMock = $this->createMock(CampusInstitutionId::class);
        $institutionIdMock->expects(self::once())
            ->method('setValue')
            ->with($dataTest['institutionId'])
            ->willReturnSelf();
        $campusMock->expects(self::once())
            ->method('institutionId')
            ->willReturn($institutionIdMock);

        $nameMock = $this->createMock(CampusName::class);
        $nameMock->expects(self::once())
            ->method('setValue')
            ->with($dataTest['name'])
            ->willReturnSelf();
        $campusMock->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);

        $addressMock = $this->createMock(CampusAddress::class);
        $addressMock->expects(self::once())
            ->method('setValue')
            ->with($dataTest['address'])
            ->willReturnSelf();
        $campusMock->expects(self::once())
            ->method('address')
            ->willReturn($addressMock);

        $phoneMock = $this->createMock(CampusPhone::class);
        $phoneMock->expects(self::once())
            ->method('setValue')
            ->with($dataTest['phone'])
            ->willReturnSelf();
        $campusMock->expects(self::once())
            ->method('phone')
            ->willReturn($phoneMock);

        $emailMock = $this->createMock(CampusEmail::class);
        $emailMock->expects(self::once())
            ->method('setValue')
            ->with($dataTest['email'])
            ->willReturnSelf();
        $campusMock->expects(self::once())
            ->method('email')
            ->willReturn($emailMock);

        $observationsMock = $this->createMock(CampusObservations::class);
        $observationsMock->expects(self::once())
            ->method('setValue')
            ->with($dataTest['observations'])
            ->willReturnSelf();
        $campusMock->expects(self::once())
            ->method('observations')
            ->willReturn($observationsMock);

        $searchMock = $this->createMock(CampusSearch::class);
        $searchMock->expects(self::once())
            ->method('setValue')
            ->with($dataTest['search'])
            ->willReturnSelf();
        $campusMock->expects(self::once())
            ->method('search')
            ->willReturn($searchMock);

        $stateMock = $this->createMock(CampusState::class);
        $stateMock->expects(self::once())
            ->method('setValue')
            ->with($dataTest['state'])
            ->willReturnSelf();
        $campusMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $createdAtMock = $this->createMock(CampusCreatedAt::class);
        $createdAtMock->expects(self::once())
            ->method('setValue')
            ->with($dataTest['createdAt'])
            ->willReturnSelf();
        $campusMock->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAtMock);

        $updatedAtMock = $this->createMock(CampusUpdatedAt::class);
        $updatedAtMock->expects(self::once())
            ->method('setValue')
            ->with($dataTest['updatedAt'])
            ->willReturnSelf();
        $campusMock->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updatedAtMock);

        $campusMock->expects(self::once())
            ->method('refreshSearch')
            ->willReturnSelf();

        $this->campusRepository->expects(self::once())
            ->method('find')
            ->with($campusId)
            ->willReturn($campusMock);

        $this->campusRepository->expects(self::once())
            ->method('persistCampus')
            ->with($campusMock)
            ->willReturn($campusMock);

        $result = $this->useCase->execute($requestMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testExecuteShouldReturnException(): void
    {
        $requestMock = $this->createMock(CreateCampusRequest::class);

        $this->campusRepository->expects(self::never())
            ->method('find');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($requestMock);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetFunctionNameShouldReturnNameValid(): void
    {
        $reflection = new \ReflectionClass(UpdateCampus::class);
        $method = $reflection->getMethod('getFunctionName');
        $this->assertTrue($method->isProtected());

        $result = $method->invoke($this->useCase, 'name');
        $this->assertIsString($result);
        $this->assertSame('changeName', $result);
    }
}
