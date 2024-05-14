<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

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

    private UseCasesService|Mock $useCaseMock;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(UserRepositoryContract::class);

        $this->useCaseMock = $this->getMockForAbstractClass(
            UseCasesService::class,
            [$this->repository],
            '',
            true,
            true,
            true,
            ['validateRequest']
        );
    }

    public function tearDown(): void
    {
        unset($this->repository, $this->useCaseMock);
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \ReflectionException
     */
    public function test_validateRequest_should_return_request(): void
    {
        $requestMock = $this->createMock(RequestService::class);

        $this->useCaseMock->expects(self::once())
            ->method('validateRequest');

        $reflectionMethod = new \ReflectionMethod(get_class($this->useCaseMock), 'validateRequest');

        $result = $reflectionMethod->invoke($this->useCaseMock, $requestMock, RequestService::class);

        $this->assertInstanceOf(RequestService::class, $result);
    }

    /**
     * @throws Exception
     * @throws \ReflectionException
     */
    public function test_validateRequest_should_return_exception(): void
    {
        $requestMock = $this->createMock(RequestService::class);

        $this->useCaseMock->expects(self::once())
            ->method('validateRequest')
            ->willThrowException(new \Exception('Request not valid'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $reflectionMethod = new \ReflectionMethod(get_class($this->useCaseMock), 'validateRequest');

        $reflectionMethod->invoke($this->useCaseMock, $requestMock, RequestService::class);
    }
}
