<?php

namespace Tests\Feature\Core\Campus\Application\UseCases\CreateCampus;

use Core\Campus\Application\UseCases\CreateCampus\CreateCampusRequest;
use Core\Campus\Domain\Campus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CreateCampusRequest::class)]
class CreateCampusRequestTest extends TestCase
{
    private Campus|MockObject $campus;
    private CreateCampusRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->campus = $this->createMock(Campus::class);
        $this->request = new CreateCampusRequest($this->campus);
    }

    public function tearDown(): void
    {
        unset(
            $this->campus,
            $this->request
        );
        parent::tearDown();
    }

    public function testCampusShouldReturnObject(): void
    {
        $result = $this->request->campus();

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
    }
}
