<?php

namespace Tests\Feature\App\Http\Requests\Employee;

use App\Http\Requests\Employee\StoreEmployeeRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\InputBag;
use Tests\TestCase;

#[CoversClass(StoreEmployeeRequest::class)]
class StoreEmployeeRequestTest extends TestCase
{
    private StoreEmployeeRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = new StoreEmployeeRequest();
    }

    public function tearDown(): void
    {
        unset($this->request);
        parent::tearDown();
    }

    public function testAuthorizeShouldReturnTrue(): void
    {
        $result = $this->request->authorize();
        $this->assertTrue($result);
    }

    public function testRulesShouldReturnArrayWhenUserIsNew(): void
    {
        $expected = [
            'employeeId' => ['nullable'],
            'userId' => ['nullable'],
            'identifier' => ['required'],
            'typeDocument' => ['required'],
            'name' => ['required'],
            'lastname' => ['required'],
            'email' => ['required', 'email:rfc'],
            'profile' => ['required'],
            'login' => ['required'],
            'password' => ['required', 'confirmed', 'min:7'],
        ];

        $requestMock = new InputBag(['userId' => 0, 'password' => '']);

        $this->request->request = $requestMock;
        $result = $this->request->rules();

        $this->assertIsArray($result);
        $this->assertCount(10, $result);
        $this->assertEquals($expected, $result);
    }

    public function testRulesShouldReturnArrayWhenUserExistAndPassword(): void
    {
        $expected = [
            'employeeId' => ['nullable'],
            'userId' => ['nullable'],
            'identifier' => ['required'],
            'typeDocument' => ['required'],
            'name' => ['required'],
            'lastname' => ['required'],
            'email' => ['required', 'email:rfc'],
            'profile' => ['required'],
            'login' => ['required'],
            'password' => ['required', 'confirmed', 'min:7'],
        ];

        $requestMock = new InputBag(['userId' => 1, 'password' => 'testing']);

        $this->request->request = $requestMock;
        $result = $this->request->rules();

        $this->assertIsArray($result);
        $this->assertCount(10, $result);
        $this->assertEquals($expected, $result);
    }

    public function testRulesShouldReturnArrayWhenUserExistWithoutPassword(): void
    {
        $expected = [
            'employeeId' => ['nullable'],
            'userId' => ['nullable'],
            'identifier' => ['required'],
            'typeDocument' => ['required'],
            'name' => ['required'],
            'lastname' => ['required'],
            'email' => ['required', 'email:rfc'],
            'profile' => ['required'],
            'login' => ['required'],
        ];

        $requestMock = new InputBag(['userId' => 1, 'password' => '']);

        $this->request->request = $requestMock;
        $result = $this->request->rules();

        $this->assertIsArray($result);
        $this->assertCount(9, $result);
        $this->assertEquals($expected, $result);
    }

    public function testMessagesShouldReturnArray(): void
    {
        $expected = [
            'identifier.required' => 'El campo identifier es requerido',
            'typeDocument.required' => 'El campo typeDocument es requerido',
            'name.required' => 'El campo name es requerido',
            'lastname.required' => 'El campo lastname es requerido',
            'email.required' => 'El campo email es requerido',
            'email.email' => 'El campo email debe ser una dirección email valida',
            'profile.required' => 'El campo profile es requerido',
            'login.required' => 'El campo login es requerido',
            'password.required' => 'El campo password es requerido',
            'password.confirmed' => 'El campo password debe ser confirmado',
            'password.min' => 'El campo password debe ser minimo de 7 caracteres',
        ];

        $result = $this->request->messages();

        $this->assertIsArray($result);
        $this->assertCount(11, $result);
        $this->assertEquals($expected, $result);
    }
}
