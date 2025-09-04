<?php

namespace Tests\Feature\Performance;

use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TranslationPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);
        
        $this->token = $this->user->createToken('API Token')->plainTextToken;
    }

    /**
     * @test
     */
    public function translation_retrieval_performance()
    {
        $translation = Translation::factory()->create([
            'context' => 'test',
            'locale' => 'en',
            'translations' => ['hello' => 'Hello World']
        ]);

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/translation/get/test/en');

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = ($endTime - $startTime) * 1000;
        $memoryUsed = $endMemory - $startMemory;

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    'id',
                    'context',
                    'locale',
                    'translations'
                ]
            ]);

        // Performance assertions
        $this->assertLessThan(300, $executionTime, 'Translation retrieval should complete within 300ms');
        $this->assertLessThan(5 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 5MB');
    }

    /**
     * @test
     */
    public function translation_creation_performance_with_small_data()
    {
        $translationData = [
            'context' => 'test',
            'locale' => 'en',
            'translations' => ['hello' => 'Hello World']
        ];

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/translation/create', $translationData);

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = ($endTime - $startTime) * 1000;
        $memoryUsed = $endMemory - $startMemory;

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    'id',
                    'context',
                    'locale',
                    'translations'
                ]
            ]);

        // Performance assertions
        $this->assertLessThan(500, $executionTime, 'Translation creation should complete within 500ms');
        $this->assertLessThan(8 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 8MB');
    }

    /**
     * @test
     */
    public function translation_creation_performance_with_large_data()
    {
        $largeTranslations = [];
        for ($i = 1; $i <= 100; $i++) {
            $largeTranslations["key_{$i}"] = "This is a very long translation value for key {$i} that contains many characters to test performance with large datasets";
        }

        $translationData = [
            'context' => 'large_test',
            'locale' => 'en',
            'translations' => $largeTranslations
        ];

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/translation/create', $translationData);

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = ($endTime - $startTime) * 1000;
        $memoryUsed = $endMemory - $startMemory;

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    'id',
                    'context',
                    'locale',
                    'translations'
                ]
            ]);

        // Performance assertions
        $this->assertLessThan(1000, $executionTime, 'Large translation creation should complete within 1000ms');
        $this->assertLessThan(15 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 15MB');
    }

    /**
     * @test
     */
    public function translation_search_performance_with_small_dataset()
    {
        // Create 10 translations
        for ($i = 1; $i <= 10; $i++) {
            Translation::factory()->create([
                'context' => "context_{$i}",
                'locale' => 'en',
                'translations' => ["key_{$i}" => "value_{$i}"]
            ]);
        }

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/translation/search/context');

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = ($endTime - $startTime) * 1000;
        $memoryUsed = $endMemory - $startMemory;

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    'current_page',
                    'data',
                    'per_page',
                    'total'
                ]
            ]);

        // Performance assertions
        $this->assertLessThan(500, $executionTime, 'Small dataset search should complete within 500ms');
        $this->assertLessThan(8 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 8MB');
    }

    /**
     * @test
     */
    public function translation_search_performance_with_large_dataset()
    {
        // Create 100 translations
        for ($i = 1; $i <= 100; $i++) {
            Translation::factory()->create([
                'context' => "context_{$i}",
                'locale' => 'en',
                'translations' => ["key_{$i}" => "value_{$i}"]
            ]);
        }

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/translation/search/context');

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = ($endTime - $startTime) * 1000;
        $memoryUsed = $endMemory - $startMemory;

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    'current_page',
                    'data',
                    'per_page',
                    'total'
                ]
            ]);

        // Performance assertions
        $this->assertLessThan(1000, $executionTime, 'Large dataset search should complete within 1000ms');
        $this->assertLessThan(12 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 12MB');
    }

    /**
     * @test
     */
    public function translation_search_performance_with_pagination()
    {
        // Create 150 translations to test pagination
        for ($i = 1; $i <= 150; $i++) {
            Translation::factory()->create([
                'context' => "context_{$i}",
                'locale' => 'en',
                'translations' => ["key_{$i}" => "value_{$i}"]
            ]);
        }

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/translation/search/context');

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = ($endTime - $startTime) * 1000;
        $memoryUsed = $endMemory - $startMemory;

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    'current_page',
                    'data',
                    'per_page',
                    'total'
                ]
            ]);

        $responseData = $response->json('data');
        $this->assertEquals(100, $responseData['per_page'], 'Should return 100 items per page');
        $this->assertEquals(150, $responseData['total'], 'Should have 150 total items');

        // Performance assertions
        $this->assertLessThan(1200, $executionTime, 'Paginated search should complete within 1200ms');
        $this->assertLessThan(15 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 15MB');
    }

    /**
     * @test
     */
    public function translation_update_performance()
    {
        $translation = Translation::factory()->create([
            'context' => 'test',
            'locale' => 'en',
            'translations' => ['hello' => 'Hello World']
        ]);

        $updateData = [
            'id' => $translation->id,
            'translations' => [
                'hello' => 'Updated Hello World',
                'goodbye' => 'Goodbye World'
            ]
        ];

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->patchJson('/api/translation/update', $updateData);

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = ($endTime - $startTime) * 1000;
        $memoryUsed = $endMemory - $startMemory;

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    'id',
                    'context',
                    'locale',
                    'translations'
                ]
            ]);

        // Performance assertions
        $this->assertLessThan(500, $executionTime, 'Translation update should complete within 500ms');
        $this->assertLessThan(8 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 8MB');
    }

    /**
     * @test
     */
    public function concurrent_translation_creation_performance()
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        // Simulate concurrent translation creation
        $responses = [];
        for ($i = 1; $i <= 10; $i++) {
            $translationData = [
                'context' => "concurrent_{$i}",
                'locale' => 'en',
                'translations' => ["key_{$i}" => "value_{$i}"]
            ];

            $responses[] = $this->withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->postJson('/api/translation/create', $translationData);
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
        $this->assertLessThan(3000, $executionTime, '10 concurrent translations should complete within 3000ms');
        $this->assertLessThan(25 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 25MB');
    }

    /**
     * @test
     */
    public function database_query_performance_under_load()
    {
        // Create 50 translations first
        for ($i = 1; $i <= 50; $i++) {
            Translation::factory()->create([
                'context' => "load_test_{$i}",
                'locale' => 'en',
                'translations' => ["key_{$i}" => "value_{$i}"]
            ]);
        }

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        // Perform multiple operations under load
        $operations = [];
        for ($i = 1; $i <= 20; $i++) {
            // Search
            $operations[] = $this->withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->getJson('/api/translation/search/load_test');

            // Get specific translation
            $operations[] = $this->withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->getJson('/api/translation/get/load_test_1/en');
        }

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = ($endTime - $startTime) * 1000;
        $memoryUsed = $endMemory - $startMemory;

        // All operations should be successful
        foreach ($operations as $operation) {
            $operation->assertStatus(200);
        }

        // Performance assertions
        $this->assertLessThan(5000, $executionTime, '40 operations under load should complete within 5000ms');
        $this->assertLessThan(30 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 30MB');
    }

    /**
     * @test
     */
    public function memory_usage_performance_test()
    {
        $initialMemory = memory_get_usage();

        // Create a translation
        $translationData = [
            'context' => 'memory_test',
            'locale' => 'en',
            'translations' => ['hello' => 'Hello World']
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/translation/create', $translationData);

        $peakMemory = memory_get_peak_usage();
        $finalMemory = memory_get_usage();

        $response->assertStatus(200);

        $memoryIncrease = $finalMemory - $initialMemory;
        $peakMemoryUsage = $peakMemory - $initialMemory;

        // Memory assertions
        $this->assertLessThan(8 * 1024 * 1024, $memoryIncrease, 'Memory increase should be less than 8MB');
        $this->assertLessThan(15 * 1024 * 1024, $peakMemoryUsage, 'Peak memory usage should be less than 15MB');
    }

    /**
     * @test
     */
    public function response_size_performance_test()
    {
        // Create a translation with moderate data
        $translationData = [
            'context' => 'response_test',
            'locale' => 'en',
            'translations' => [
                'welcome' => 'Welcome to our application',
                'description' => 'This is a comprehensive description of our application features and capabilities',
                'features' => 'Advanced features include real-time updates, multi-language support, and robust API endpoints'
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/translation/create', $translationData);

        $response->assertStatus(200);

        $responseContent = $response->getContent();
        $responseSize = strlen($responseContent);

        // Response size assertions
        $this->assertLessThan(4096, $responseSize, 'Response size should be less than 4KB');
        $this->assertGreaterThan(200, $responseSize, 'Response should contain meaningful data');
    }
}
