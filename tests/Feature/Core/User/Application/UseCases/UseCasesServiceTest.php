<?php

namespace Tests\Feature\Core\User\Application\UseCases;

use Core\User\Application\UseCases\RequestService;
use Core\User\Application\UseCases\UseCasesService;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Mockery\Mock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

#[CoversClass(UseCasesService::class)]
class UseCasesServiceTest extends TestCase
{
    private UserRepositoryContract|Mock $repository;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(UserRepositoryContract::class);
    }

    public function tearDown(): void
    {
        unset($this->repository);
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \ReflectionException
     */
    public function test_validateRequest_should_return_request(): void
    {
        $requestMock = $this->createMock(RequestService::class);
        $mock = $this->getMockForAbstractClass(
            UseCasesService::class,
            [$this->repository],
            '',
            true,
            true,
            true,
            ['validateRequest']
        );

        $mock->expects(self::once())
            ->method('validateRequest')
            ->with($requestMock, RequestService::class)
            ->willReturn($requestMock);

        $reflectionMethod = new \ReflectionMethod(get_class($mock),'validateRequest');
        $reflectionMethod->setAccessible(true);
        $result = $reflectionMethod->invoke($mock, $requestMock, RequestService::class);

        $this->assertInstanceOf(RequestService::class, $result);
        $this->assertSame($result, $requestMock);
    }
}
