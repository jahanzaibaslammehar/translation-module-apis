<?php

namespace Tests\Unit\Services;

use App\Core\Responses\AbstractResponse;
use App\Helpers\ResponseCode;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Responses\UserResponse;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $authService;
    protected $request;
    protected $response;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->request = new Request();
        $this->response = new UserResponse();
        $this->authService = new AuthService($this->request, $this->response);
    }

    /**
     * @test
     */
    public function it_can_login_user_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $result = $this->authService->login([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertInstanceOf(UserResponse::class, $result);
        $this->assertEquals(ResponseCode::SUCCESS, $result->getResponseType());
        $this->assertEquals(200, $result->code());
    }

    /**
     * @test
     */
    public function it_throws_unauthorized_exception_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid credentials');

        $this->authService->login([
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);
    }

    /**
     * @test
     */
    public function it_throws_unauthorized_exception_with_nonexistent_email()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid credentials');

        $this->authService->login([
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);
    }

    /**
     * @test
     */
    public function it_generates_api_token_on_successful_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $result = $this->authService->login([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertInstanceOf(UserResponse::class, $result);
        
        // Verify token was generated in database
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
        ]);
    }

    /**
     * @test
     */
    public function it_returns_user_data_with_token_in_response()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $result = $this->authService->login([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $responseData = $result->getData();
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('name', $responseData);
        $this->assertArrayHasKey('email', $responseData);
        $this->assertArrayHasKey('access_token', $responseData);
    }

    /**
     * @test
     */
    public function it_handles_empty_credentials()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid credentials');

        $this->authService->login([
            'email' => '',
            'password' => '',
        ]);
    }

    /**
     * @test
     */
    public function it_handles_malformed_credentials()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid credentials');

        $this->authService->login([
            'email' => null,
            'password' => null,
        ]);
    }
}
