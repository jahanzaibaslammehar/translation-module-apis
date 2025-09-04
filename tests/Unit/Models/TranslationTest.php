<?php

namespace Tests\Unit\Models;

use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationTest extends TestCase
{
    use RefreshDatabase;

    private Translation $translation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->translation = new Translation();
    }

    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $expectedFillable = ['locale', 'context', 'translations'];
        
        $this->assertEquals($expectedFillable, $this->translation->getFillable());
    }

    /** @test */
    public function it_has_correct_casts()
    {
        $expectedCasts = [
            'id' => 'int',
            'translations' => 'array',
        ];
        
        $this->assertEquals($expectedCasts, $this->translation->getCasts());
    }

    /** @test */
    public function it_uses_correct_table_name()
    {
        $this->assertEquals('translations', $this->translation->getTable());
    }

    /** @test */
    public function it_can_create_translation_with_valid_data()
    {
        $translationData = [
            'locale' => 'en',
            'context' => 'common',
            'translations' => [
                'welcome' => 'Welcome',
                'hello' => 'Hello',
            ],
        ];

        $translation = Translation::create($translationData);

        $this->assertInstanceOf(Translation::class, $translation);
        $this->assertEquals($translationData['locale'], $translation->locale);
        $this->assertEquals($translationData['context'], $translation->context);
        $this->assertEquals($translationData['translations'], $translation->translations);
        
        $this->assertDatabaseHas('translations', [
            'context' => 'common',
            'locale' => 'en',
        ]);
    }

    /** @test */
    public function it_can_update_translation_attributes()
    {
        $translation = Translation::factory()->create();
        
        $newTranslations = [
            'goodbye' => 'Goodbye',
            'thanks' => 'Thank you',
        ];
        
        $translation->update(['translations' => $newTranslations]);
        
        $this->assertEquals($newTranslations, $translation->fresh()->translations);
    }

    /** @test */
    public function it_can_access_translations_as_array()
    {
        $translation = Translation::create([
            'locale' => 'en',
            'context' => 'common',
            'translations' => [
                'welcome' => 'Welcome',
            ],
        ]);

        $this->assertIsArray($translation->translations);
        $this->assertEquals('Welcome', $translation->translations['welcome']);
    }

    /** @test */
    public function it_has_factory_trait()
    {
        $this->assertTrue(method_exists($this->translation, 'factory'));
    }

    /** @test */
    public function it_can_be_serialized_to_json()
    {
        $translation = Translation::create([
            'locale' => 'en',
            'context' => 'common',
            'translations' => ['test' => 'value'],
        ]);

        $json = $translation->toJson();
        
        $this->assertIsString($json);
        $this->assertStringContainsString('en', $json);
        $this->assertStringContainsString('common', $json);
    }
}
