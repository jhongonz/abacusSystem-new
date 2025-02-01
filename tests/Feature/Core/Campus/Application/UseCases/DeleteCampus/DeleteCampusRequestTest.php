<?php

namespace Tests\Feature\Core\Campus\Application\UseCases\DeleteCampus;

use Core\Campus\Application\UseCases\DeleteCampus\DeleteCampusRequest;
use Core\Campus\Domain\ValueObjects\CampusId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DeleteCampusRequest::class)]
class DeleteCampusRequestTest extends TestCase
{
    private CampusId|MockObject $campusId;
    private DeleteCampusRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->campusId = $this->createMock(CampusId::class);
        $this->request = new DeleteCampusRequest($this->campusId);
    }

    public function tearDown(): void
    {
        unset(
            $this->campusId,
            $this->request
        );
        parent::tearDown();
    }

    public function testIdShouldReturnObject(): void
    {
        $result = $this->request->id();

        $this->assertInstanceOf(CampusId::class, $result);
        $this->assertSame($this->campusId, $result);
    }
}
