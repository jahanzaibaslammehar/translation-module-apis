<?php

namespace Tests\Feature\Translation;

use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TranslationManagementTest extends TestCase
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
    public function unauthenticated_user_cannot_access_translation_routes()
    {
        $response = $this->getJson('/api/translation/get/test/en');
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function user_can_get_translation_by_context_and_locale()
    {
        $translation = Translation::factory()->create([
            'context' => 'test',
            'locale' => 'en',
            'translations' => ['hello' => 'Hello World']
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/translation/get/test/en');

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
    }

    /**
     * @test
     */
    public function user_can_create_new_translation()
    {
        $translationData = [
            'context' => 'new_context',
            'locale' => 'es',
            'translations' => [
                'welcome' => 'Bienvenido',
                'goodbye' => 'Adiós'
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/translation/create', $translationData);

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

        $this->assertDatabaseHas('translations', [
            'context' => 'new_context',
            'locale' => 'es'
        ]);
    }

    /**
     * @test
     */
    public function user_can_update_existing_translation()
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

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->patchJson('/api/translation/update', $updateData);

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

        // Check that the translation was updated in the database
        $updatedTranslation = Translation::find($translation->id);
        $this->assertNotNull($updatedTranslation);
        $this->assertEquals('Updated Hello World', $updatedTranslation->translations['hello']);
        $this->assertEquals('Goodbye World', $updatedTranslation->translations['goodbye']);
    }

    /**
     * @test
     */
    public function user_can_search_translations()
    {
        Translation::factory()->create([
            'context' => 'welcome',
            'locale' => 'en',
            'translations' => ['hello' => 'Hello']
        ]);

        Translation::factory()->create([
            'context' => 'welcome',
            'locale' => 'es',
            'translations' => ['hello' => 'Hola']
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/translation/search/welcome');

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
    }

    /**
     * @test
     */
    public function create_translation_validates_required_fields()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/translation/create', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['context', 'locale', 'translations']);
    }

    /**
     * @test
     */
    public function create_translation_validates_field_types()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/translation/create', [
            'context' => 123,
            'locale' => 456,
            'translations' => 'not_an_array'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['context', 'locale', 'translations']);
    }

    /**
     * @test
     */
    public function create_translation_validates_translation_structure()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/translation/create', [
            'context' => 'test',
            'locale' => 'en',
            'translations' => [
                'valid_key' => '',  // Empty value should fail validation
                '' => 'valid_value'  // Empty key should fail validation
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['translations.valid_key']);
    }

    /**
     * @test
     */
    public function update_translation_validates_id_exists()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->patchJson('/api/translation/update', [
            'id' => 99999,
            'translations' => ['hello' => 'Updated']
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['id']);
    }

    /**
     * @test
     */
    public function update_translation_validates_id_is_required()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->patchJson('/api/translation/update', [
            'translations' => ['hello' => 'Updated']
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['id']);
    }

    /**
     * @test
     */
    public function search_returns_paginated_results()
    {
        // Create multiple translations for pagination testing
        for ($i = 1; $i <= 15; $i++) {
            Translation::factory()->create([
                'context' => "context_{$i}",
                'locale' => 'en',
                'translations' => ["key_{$i}" => "value_{$i}"]
            ]);
        }

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/translation/search/context');

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
        $this->assertGreaterThan(0, $responseData['total']);
        $this->assertLessThanOrEqual(100, $responseData['per_page']); // Default pagination
    }

    /**
     * @test
     */
    public function search_returns_empty_results_for_nonexistent_keyword()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/translation/search/nonexistent_keyword');

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
        $this->assertEquals(0, $responseData['total']);
    }

    /**
     * @test
     */
    public function search_finds_translations_by_context()
    {
        Translation::factory()->create([
            'context' => 'welcome_page',
            'locale' => 'en',
            'translations' => ['title' => 'Welcome']
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/translation/search/welcome_page');

        $response->assertStatus(200);
        $responseData = $response->json('data');
        $this->assertGreaterThan(0, $responseData['total']);
    }

    /**
     * @test
     */
    public function search_finds_translations_by_locale()
    {
        Translation::factory()->create([
            'context' => 'common',
            'locale' => 'fr',
            'translations' => ['hello' => 'Bonjour']
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/translation/search/fr');

        $response->assertStatus(200);
        $responseData = $response->json('data');
        $this->assertGreaterThan(0, $responseData['total']);
    }

    /**
     * @test
     */
    public function search_finds_translations_by_key()
    {
        Translation::factory()->create([
            'context' => 'common',
            'locale' => 'en',
            'translations' => ['welcome_message' => 'Welcome to our app']
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/translation/search/welcome_message');

        $response->assertStatus(200);
        $responseData = $response->json('data');
        $this->assertGreaterThan(0, $responseData['total']);
    }

    /**
     * @test
     */
    public function search_finds_translations_by_translation_value()
    {
        Translation::factory()->create([
            'context' => 'common',
            'locale' => 'en',
            'translations' => ['welcome' => 'Welcome to our app']
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/translation/search/Welcome to our app');

        $response->assertStatus(200);
        $responseData = $response->json('data');
        $this->assertGreaterThan(0, $responseData['total']);
    }

    /**
     * @test
     */
    public function translation_routes_require_valid_token()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid_token',
        ])->getJson('/api/translation/get/test/en');

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function translation_routes_require_bearer_token_format()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Invalid-Format invalid_token',
        ])->getJson('/api/translation/get/test/en');

        $response->assertStatus(401);
    }
}
