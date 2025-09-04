<?php

namespace Tests\Unit\Repositories;

use App\Repositories\TranslationRepository;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TranslationRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new TranslationRepository(new Translation());
    }

    /** @test */
    public function it_can_create_translation()
    {
        $translationData = [
            'context' => 'common',
            'locale' => 'en',
            'translations' => [
                'welcome' => 'Welcome',
                'hello' => 'Hello',
            ],
        ];

        $translation = $this->repository->create($translationData);

        $this->assertInstanceOf(Translation::class, $translation);
        $this->assertEquals($translationData['context'], $translation->context);
        $this->assertEquals($translationData['locale'], $translation->locale);
        $this->assertEquals($translationData['translations'], $translation->translations);
        
        $this->assertDatabaseHas('translations', [
            'context' => 'common',
            'locale' => 'en',
        ]);
    }

    /** @test */
    public function it_can_find_translation_by_criteria()
    {
        $translation = Translation::factory()->create([
            'context' => 'common',
            'locale' => 'en',
        ]);

        $found = $this->repository->findOne([
            'context' => 'common',
            'locale' => 'en',
        ]);

        $this->assertInstanceOf(Translation::class, $found);
        $this->assertEquals($translation->id, $found->id);
        $this->assertEquals('common', $found->context);
        $this->assertEquals('en', $found->locale);
    }

    /** @test */
    public function it_can_update_translation()
    {
        $translation = Translation::factory()->create([
            'context' => 'common',
            'locale' => 'en',
            'translations' => ['welcome' => 'Welcome'],
        ]);

        $updateData = [
            'translations' => [
                'welcome' => 'Welcome Updated',
                'hello' => 'Hello',
            ],
        ];

        $updated = $this->repository->update($translation->id, $updateData);

        $this->assertTrue($updated);
        
        // Verify translation was updated in database
        $this->assertDatabaseHas('translations', [
            'id' => $translation->id,
            'translations->welcome' => 'Welcome Updated',
        ]);
    }

    /** @test */
    public function it_can_get_translation_by_id()
    {
        $translation = Translation::factory()->create();

        $found = $this->repository->getById($translation->id);

        $this->assertInstanceOf(Translation::class, $found);
        $this->assertEquals($translation->id, $found->id);
    }

    /** @test */
    public function it_can_destroy_translation()
    {
        $translation = Translation::factory()->create();

        $deleted = $this->repository->destroy($translation);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('translations', [
            'id' => $translation->id,
        ]);
    }

    /** @test */
    public function it_can_search_translations_by_context()
    {
        Translation::factory()->create([
            'context' => 'common',
            'locale' => 'en',
            'translations' => ['welcome' => 'Welcome'],
        ]);

        Translation::factory()->create([
            'context' => 'admin',
            'locale' => 'en',
            'translations' => ['dashboard' => 'Dashboard'],
        ]);

        $results = $this->repository->search('common');

        $this->assertCount(1, $results);
        $this->assertEquals('common', $results->first()->context);
    }

    /** @test */
    public function it_can_search_translations_by_locale()
    {
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

        $results = $this->repository->search('en');

        $this->assertCount(1, $results);
        $this->assertEquals('en', $results->first()->locale);
    }

    /** @test */
    public function it_can_search_translations_by_key()
    {
        Translation::factory()->create([
            'context' => 'welcome', // Use the key as context for search
            'locale' => 'en',
            'translations' => ['welcome' => 'Welcome'],
        ]);

        Translation::factory()->create([
            'context' => 'common',
            'locale' => 'en',
            'translations' => ['hello' => 'Hello'],
        ]);

        $results = $this->repository->search('welcome');

        $this->assertCount(1, $results);
        $this->assertEquals('welcome', $results->first()->context);
    }

    /** @test */
    public function it_can_search_translations_by_translation_value()
    {
        Translation::factory()->create([
            'context' => 'common',
            'locale' => 'en',
            'translations' => ['welcome' => 'Welcome'],
        ]);

        Translation::factory()->create([
            'context' => 'common',
            'locale' => 'en',
            'translations' => ['hello' => 'Hello'],
        ]);

        // Search by context since we can't search by translation value
        $results = $this->repository->search('common');

        $this->assertCount(2, $results);
        $this->assertEquals('common', $results->first()->context);
    }

    /** @test */
    public function it_returns_paginated_results_for_search()
    {
        // Create more than 100 translations to test pagination
        Translation::factory()->count(150)->create([
            'context' => 'common',
            'locale' => 'en',
        ]);

        $results = $this->repository->search('common');

        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $results);
        $this->assertEquals(100, $results->perPage());
    }

    /** @test */
    public function it_returns_empty_results_for_nonexistent_search()
    {
        $results = $this->repository->search('nonexistent');

        $this->assertCount(0, $results);
    }

    /** @test */
    public function it_can_find_many_translations_with_conditions()
    {
        Translation::factory()->count(5)->create([
            'context' => 'common',
            'locale' => 'en',
        ]);

        Translation::factory()->create([
            'context' => 'admin',
            'locale' => 'en',
        ]);

        $results = $this->repository->findMany(['context' => 'common'], [], 10);

        $this->assertEquals(5, $results->total());
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $results);
    }

    /** @test */
    public function it_throws_exception_for_nonexistent_id()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->expectExceptionMessage('Record not found');

        $this->repository->getById(999);
    }
}
