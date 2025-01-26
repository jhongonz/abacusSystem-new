<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2025-01-07 19:58:57
 */

namespace Tests\Feature\Core\SharedContext\Infrastructure\Persistence;

use Core\SharedContext\Infrastructure\Persistence\AbstractChainRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractChainRepository::class)]
class AbstractChainRepositoryTest extends TestCase
{
    private AbstractChainRepository|MockObject $repository;

    protected function tearDown(): void
    {
        unset($this->repository);
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \ReflectionException
     */
    public function testCanPersistShouldReturnBool(): void
    {
        $this->repository = $this->getMockBuilder(AbstractChainRepository::class)->getMock();

        $reflexion = new \ReflectionClass(AbstractChainRepository::class);
        $method = $reflexion->getMethod('canPersist');
        $this->assertTrue($method->isProtected());

        $result = $method->invoke($this->repository);
        $this->assertTrue($result);
    }
}
