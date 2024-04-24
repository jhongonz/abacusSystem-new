<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Infrastructure\Repositories\Translators;

use App\Models\User as UserModel;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserLogin;
use Core\User\Infrastructure\Persistence\Translators\DomainToModelUserTranslator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DomainToModelUserTranslator::class)]
class DomainToModelUserTranslatorTest extends TestCase
{
    private UserModel|MockObject $userModel;
    private string $canTranslate;
    private string $mainSearchField;
    private DomainToModelUserTranslator $translator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->canTranslate = User::class;
        $this->mainSearchField = 'user_login';
        $this->userModel = $this->createMock(UserModel::class);
        $this->translator = new DomainToModelUserTranslator($this->userModel);
    }

    public function tearDown(): void
    {
        unset(
            $this->userModel,
            $this->translator,
            $this->canTranslate,
            $this->mainSearchField
        );
        parent::tearDown();
    }

    public function test_can_translate_should_return_string_class(): void
    {
        $result = $this->translator->canTranslate();
        $this->assertSame($this->canTranslate, $result);
        $this->assertIsString($result);
    }

    public function test_can_translateTo_should_return_string_class_model(): void
    {
        $result = $this->translator->canTranslateTo();
        $this->assertSame(UserModel::class, $result);
        $this->assertIsString($result);
    }

    /**
     * @throws Exception
     */
    /*public function test_executeTranslate_should_return_class_model_when_model_is_null(): void
    {
        $loginMock = $this->createMock(UserLogin::class);
        $loginMock->expects(self::once())
            ->method('value')
            ->willReturn('login-test');

        $domainMock = $this->createMock(User::class);

        $modelMock = \Mockery::mock(UserModel::class);
        $modelMock->shouldReceive('where')
            ->once()
            ->with($this->mainSearchField, 'login-test')
            ->andReturnSelf();

        $modelMock->shouldReceive('first')
            ->once()
            ->andReturn((object) []);

        UserModel::where($this->mainSearchField, 'login-test')->first();
        $result = $this->translator->executeTranslate($domainMock);
        dd($result);
    }*/
}
