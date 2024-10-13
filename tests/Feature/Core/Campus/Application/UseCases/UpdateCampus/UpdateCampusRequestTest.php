<?php

namespace Tests\Feature\Core\Campus\Application\UseCases\UpdateCampus;

use Core\Campus\Application\UseCases\UpdateCampus\UpdateCampusRequest;
use Core\Campus\Domain\ValueObjects\CampusId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(UpdateCampusRequest::class)]
class UpdateCampusRequestTest extends TestCase
{
    private CampusId|MockObject $campusId;
    private UpdateCampusRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->campusId = $this->createMock(CampusId::class);
        $this->request = new UpdateCampusRequest($this->campusId, []);
    }

    public function tearDown(): void
    {
        unset(
            $this->campusId,
            $this->request
        );
        parent::tearDown();
    }

    public function test_id_should_return_object(): void
    {
        $result = $this->request->id();

        $this->assertInstanceOf(CampusId::class, $result);
        $this->assertSame($this->campusId, $result);
    }

    public function test_data_should_return_array(): void
    {
        $result = $this->request->data();

        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }
}
