<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'access_token'
                ]
            ]);
    }

    /**
     * @test
     */
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function user_cannot_login_with_nonexistent_email()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function login_requires_email_field()
    {
        $response = $this->postJson('/api/login', [
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * @test
     */
    public function login_requires_password_field()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * @test
     */
    public function login_requires_valid_email_format()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * @test
     */
    public function authenticated_user_can_access_protected_route()
    {
        $user = User::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        // Create a translation first so the route can find it
        $translation = \App\Models\Translation::factory()->create([
            'context' => 'test',
            'locale' => 'en',
            'translations' => ['hello' => 'Hello World']
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/translation/get/test/en');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function unauthenticated_user_cannot_access_protected_route()
    {
        $response = $this->getJson('/api/translation/get/test/en');

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function user_can_login_with_token_and_access_protected_routes()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Login to get token
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('data.access_token');

        // Create a translation first so the route can find it
        $translation = \App\Models\Translation::factory()->create([
            'context' => 'test',
            'locale' => 'en',
            'translations' => ['hello' => 'Hello World']
        ]);

        // Use token to access protected route
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/translation/get/test/en');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function multiple_login_attempts_create_multiple_tokens()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // First login
        $response1 = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Second login
        $response2 = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        $token1 = $response1->json('data.access_token');
        $token2 = $response2->json('data.access_token');

        $this->assertNotEquals($token1, $token2);
    }

    /**
     * @test
     */
    public function login_response_contains_correct_structure()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'access_token'
                ]
            ]);

        $responseData = $response->json();
        $this->assertEquals(200, $responseData['code']);
        $this->assertIsString($responseData['message']);
        $this->assertIsArray($responseData['data']);
    }

    /**
     * @test
     */
    public function login_handles_empty_request_data()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * @test
     */
    public function login_handles_malformed_request_data()
    {
        $response = $this->postJson('/api/login', [
            'email' => 123,
            'password' => 456,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * @test
     */
    public function login_handles_whitespace_only_values()
    {
        $response = $this->postJson('/api/login', [
            'email' => '   ',
            'password' => '   ',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * @test
     */
    public function login_handles_null_values()
    {
        $response = $this->postJson('/api/login', [
            'email' => null,
            'password' => null,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * @test
     */
    public function login_handles_very_long_values()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $longEmail = str_repeat('a', 1000) . '@example.com';
        $longPassword = str_repeat('a', 1000);

        $response = $this->postJson('/api/login', [
            'email' => $longEmail,
            'password' => $longPassword,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }
}
