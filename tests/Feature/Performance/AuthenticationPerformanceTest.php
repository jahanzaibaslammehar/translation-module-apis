<?php

namespace Tests\Feature\Performance;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationPerformanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function login_performance_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsed = $endMemory - $startMemory;

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

        // Performance assertions
        $this->assertLessThan(1000, $executionTime, 'Login should complete within 1000ms');
        $this->assertLessThan(10 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 10MB');
    }

    /**
     * @test
     */
    public function login_performance_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = ($endTime - $startTime) * 1000;
        $memoryUsed = $endMemory - $startMemory;

        $response->assertStatus(401);

        // Performance assertions
        $this->assertLessThan(500, $executionTime, 'Invalid login should fail quickly within 500ms');
        $this->assertLessThan(5 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 5MB for failed login');
    }

    /**
     * @test
     */
    public function login_performance_with_nonexistent_user()
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = ($endTime - $startTime) * 1000;
        $memoryUsed = $endMemory - $startMemory;

        $response->assertStatus(401);

        // Performance assertions
        $this->assertLessThan(500, $executionTime, 'Nonexistent user login should fail quickly within 500ms');
        $this->assertLessThan(5 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 5MB for failed login');
    }

    /**
     * @test
     */
    public function concurrent_login_performance()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        // Simulate concurrent login attempts
        $responses = [];
        for ($i = 0; $i < 10; $i++) {
            $responses[] = $this->postJson('/api/login', [
                'email' => 'test@example.com',
                'password' => 'password123',
            ]);
        }

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = ($endTime - $startTime) * 1000;
        $memoryUsed = $endMemory - $startMemory;

        // All responses should be successful
        foreach ($responses as $response) {
            $response->assertStatus(200);
        }

        // Performance assertions
        $this->assertLessThan(2000, $executionTime, '10 concurrent logins should complete within 2000ms');
        $this->assertLessThan(20 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 20MB for concurrent logins');
    }

    /**
     * @test
     */
    public function token_generation_performance()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = ($endTime - $startTime) * 1000;
        $memoryUsed = $endMemory - $startMemory;

        $response->assertStatus(200);
        $token = $response->json('data.access_token');

        $this->assertNotEmpty($token, 'Token should be generated');
        $this->assertLessThan(1000, $executionTime, 'Token generation should complete within 1000ms');
        $this->assertLessThan(10 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 10MB');
    }

    /**
     * @test
     */
    public function multiple_token_generation_performance()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $tokens = [];
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/login', [
                'email' => 'test@example.com',
                'password' => 'password123',
            ]);
            $response->assertStatus(200);
            $tokens[] = $response->json('data.access_token');
        }

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = ($endTime - $startTime) * 1000;
        $memoryUsed = $endMemory - $startMemory;

        // All tokens should be unique
        $this->assertEquals(5, count(array_unique($tokens)), 'All tokens should be unique');
        $this->assertLessThan(3000, $executionTime, '5 token generations should complete within 3000ms');
        $this->assertLessThan(15 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 15MB');
    }

    /**
     * @test
     */
    public function login_validation_performance()
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $response = $this->postJson('/api/login', [
            'email' => 'invalid-email',
            'password' => '',
        ]);

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = ($endTime - $startTime) * 1000;
        $memoryUsed = $endMemory - $startMemory;

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);

        // Performance assertions
        $this->assertLessThan(100, $executionTime, 'Validation should complete within 100ms');
        $this->assertLessThan(2 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 2MB for validation');
    }

    /**
     * @test
     */
    public function login_with_large_password_performance()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $largePassword = str_repeat('a', 1000);

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => $largePassword,
        ]);

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = ($endTime - $startTime) * 1000;
        $memoryUsed = $endMemory - $startMemory;

        $response->assertStatus(422);

        // Performance assertions
        $this->assertLessThan(500, $executionTime, 'Large password login should fail quickly within 500ms');
        $this->assertLessThan(8 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 8MB for large password');
    }

    /**
     * @test
     */
    public function login_memory_usage_performance()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $initialMemory = memory_get_usage();

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $peakMemory = memory_get_peak_usage();
        $finalMemory = memory_get_usage();

        $response->assertStatus(200);

        $memoryIncrease = $finalMemory - $initialMemory;
        $peakMemoryUsage = $peakMemory - $initialMemory;

        // Memory assertions
        $this->assertLessThan(5 * 1024 * 1024, $memoryIncrease, 'Memory increase should be less than 5MB');
        $this->assertLessThan(10 * 1024 * 1024, $peakMemoryUsage, 'Peak memory usage should be less than 10MB');
    }

    /**
     * @test
     */
    public function login_response_size_performance()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);

        $responseContent = $response->getContent();
        $responseSize = strlen($responseContent);

        // Response size assertions
        $this->assertLessThan(2048, $responseSize, 'Response size should be less than 2KB');
        $this->assertGreaterThan(100, $responseSize, 'Response should contain meaningful data');
    }
}
