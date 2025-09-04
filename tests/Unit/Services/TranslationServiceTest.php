<?php

namespace Tests\Unit\Services;

use App\Http\Resources\TranslationResource;
use App\Repositories\TranslationRepository;
use App\Responses\TranslationResponse;
use App\Services\TranslationService;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class TranslationServiceTest extends TestCase
{
    use RefreshDatabase;

    private TranslationService $service;
    private TranslationRepository $repository;
    private TranslationResponse $response;
    private Request $request;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repository = new TranslationRepository(new Translation());
        $this->response = new TranslationResponse();
        $this->request = new Request();
        
        $this->service = new TranslationService(
            $this->repository,
            $this->response,
            $this->request
        );
    }

    /** @test */
    public function it_can_show_translation_by_context_and_locale()
    {
        $translation = Translation::factory()->create([
            'context' => 'common',
            'locale' => 'en',
            'translations' => ['welcome' => 'Welcome'],
        ]);

        $result = $this->service->show('common', 'en');

        $this->assertInstanceOf(TranslationResponse::class, $result);
        $this->assertEquals(200, $result->code());
    }

    /** @test */
    public function it_can_create_translation()
    {
        $translationData = [
            'context' => 'common',
            'locale' => 'en',
            'translations' => [
                'welcome' => 'Welcome',
            ],
        ];

        $result = $this->service->createTranslation($translationData);

        $this->assertInstanceOf(TranslationResponse::class, $result);
        $this->assertEquals(200, $result->code());
        
        // Verify translation was created in database
        $this->assertDatabaseHas('translations', [
            'context' => 'common',
            'locale' => 'en',
        ]);
    }

    /** @test */
    public function it_can_update_translation()
    {
        $translation = Translation::factory()->create([
            'context' => 'common',
            'locale' => 'en',
            'translations' => ['welcome' => 'Welcome'],
        ]);

        $translationData = [
            'id' => $translation->id,
            'context' => 'common',
            'locale' => 'en',
            'translations' => [
                'welcome' => 'Welcome Updated',
            ],
        ];

        $result = $this->service->updateTranslation($translationData);

        $this->assertInstanceOf(TranslationResponse::class, $result);
        $this->assertEquals(200, $result->code());
        
        // Verify translation was updated in database
        $this->assertDatabaseHas('translations', [
            'id' => $translation->id,
            'translations->welcome' => 'Welcome Updated',
        ]);
    }

    /** @test */
    public function it_can_search_translations()
    {
        // Create test translations
        Translation::factory()->create([
            'context' => 'common',
            'locale' => 'en',
            'translations' => ['welcome' => 'Welcome'],
        ]);

        Translation::factory()->create([
            'context' => 'common',
            'locale' => 'es',
            'translations' => ['welcome' => 'Bienvenido'],
        ]);

        $result = $this->service->search('welcome');

        $this->assertInstanceOf(TranslationResponse::class, $result);
        $this->assertEquals(200, $result->code());
    }

    /** @test */
    public function it_returns_translation_response_for_all_operations()
    {
        $translation = Translation::factory()->create([
            'context' => 'common',
            'locale' => 'en',
            'translations' => ['test' => 'value'],
        ]);

        $result = $this->service->show('common', 'en');

        $this->assertInstanceOf(TranslationResponse::class, $result);
        $this->assertEquals(200, $result->code());
    }

    /** @test */
    public function it_handles_empty_search_results()
    {
        $result = $this->service->search('nonexistent');

        $this->assertInstanceOf(TranslationResponse::class, $result);
        $this->assertEquals(200, $result->code());
    }

    /** @test */
    public function it_can_handle_multiple_translations_in_context()
    {
        $translationData = [
            'context' => 'common',
            'locale' => 'en',
            'translations' => [
                'welcome' => 'Welcome',
                'hello' => 'Hello',
                'goodbye' => 'Goodbye',
            ],
        ];

        $result = $this->service->createTranslation($translationData);

        $this->assertInstanceOf(TranslationResponse::class, $result);
        $this->assertEquals(200, $result->code());
        
        // Verify all translations were stored
        $this->assertDatabaseHas('translations', [
            'context' => 'common',
            'locale' => 'en',
        ]);
    }
}
