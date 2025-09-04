<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdateTranslationRequest;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateTranslationRequestTest extends TestCase
{
    use RefreshDatabase;

    private UpdateTranslationRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new UpdateTranslationRequest();
    }

    /** @test */
    public function it_authorizes_all_users()
    {
        $this->assertTrue($this->request->authorize());
    }

    /** @test */
    public function it_has_required_validation_rules()
    {
        $rules = $this->request->rules();

        $this->assertArrayHasKey('id', $rules);
        $this->assertArrayHasKey('context', $rules);
        $this->assertArrayHasKey('locale', $rules);
        $this->assertArrayHasKey('translations', $rules);
    }

    /** @test */
    public function it_validates_id_is_required()
    {
        $data = [
            'context' => 'common',
            'locale' => 'en',
            'translations' => [
                'welcome' => 'Welcome',
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('id'));
    }

    /** @test */
    public function it_validates_id_is_integer()
    {
        $data = [
            'id' => 'not-an-integer',
            'context' => 'common',
            'locale' => 'en',
            'translations' => [
                'welcome' => 'Welcome',
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('id'));
    }

    /** @test */
    public function it_validates_id_exists_in_translations_table()
    {
        $data = [
            'id' => 999,
            'context' => 'common',
            'locale' => 'en',
            'translations' => [
                ['key' => 'welcome', 'translation' => 'Welcome'],
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('id'));
    }

    /** @test */
    public function it_passes_validation_when_id_exists()
    {
        $translation = Translation::factory()->create();

        $data = [
            'id' => $translation->id,
            'context' => 'common',
            'locale' => 'en',
            'translations' => [
                'welcome' => 'Welcome',
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_context_is_string_when_provided()
    {
        $translation = Translation::factory()->create();

        $data = [
            'id' => $translation->id,
            'context' => 123,
            'locale' => 'en',
            'translations' => [
                'welcome' => 'Welcome',
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('context'));
    }

    /** @test */
    public function it_validates_context_max_length_when_provided()
    {
        $translation = Translation::factory()->create();

        $data = [
            'id' => $translation->id,
            'context' => str_repeat('a', 256),
            'locale' => 'en',
            'translations' => [
                ['key' => 'welcome', 'translation' => 'Welcome'],
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('context'));
    }

    /** @test */
    public function it_validates_locale_is_string_when_provided()
    {
        $translation = Translation::factory()->create();

        $data = [
            'id' => $translation->id,
            'context' => 'common',
            'locale' => 123,
            'translations' => [
                'welcome' => 'Welcome',
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('locale'));
    }

    /** @test */
    public function it_validates_locale_max_length_when_provided()
    {
        $translation = Translation::factory()->create();

        $data = [
            'id' => $translation->id,
            'context' => 'common',
            'locale' => str_repeat('a', 256),
            'translations' => [
                'welcome' => 'Welcome',
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('locale'));
    }

    /** @test */
    public function it_validates_translations_is_array_when_provided()
    {
        $translation = Translation::factory()->create();

        $data = [
            'id' => $translation->id,
            'context' => 'common',
            'locale' => 'en',
            'translations' => 'not-an-array',
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('translations'));
    }

    /** @test */
    public function it_validates_translation_key_is_string_when_provided()
    {
        $translation = Translation::factory()->create();

        $data = [
            'id' => $translation->id,
            'context' => 'common',
            'locale' => 'en',
            'translations' => [
                ['key' => 123, 'translation' => 'Welcome'],
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('translations.0.key'));
    }

    /** @test */
    public function it_validates_translation_value_is_string_when_provided()
    {
        $translation = Translation::factory()->create();

        $data = [
            'id' => $translation->id,
            'context' => 'common',
            'locale' => 'en',
            'translations' => [
                ['key' => 'welcome', 'translation' => 123],
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('translations.0.translation'));
    }

    /** @test */
    public function it_validates_translation_value_max_length_when_provided()
    {
        $translation = Translation::factory()->create();

        $data = [
            'id' => $translation->id,
            'context' => 'common',
            'locale' => 'en',
            'translations' => [
                ['key' => 'welcome', 'translation' => str_repeat('a', 256)],
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('translations.0.translation'));
    }

    /** @test */
    public function it_passes_validation_with_minimal_data()
    {
        $translation = Translation::factory()->create();

        $data = [
            'id' => $translation->id,
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_passes_validation_with_all_data()
    {
        $translation = Translation::factory()->create();

        $data = [
            'id' => $translation->id,
            'context' => 'common',
            'locale' => 'en',
            'translations' => [
                ['key' => 'welcome', 'translation' => 'Welcome'],
                ['key' => 'hello', 'translation' => 'Hello'],
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_passes_validation_with_partial_data()
    {
        $translation = Translation::factory()->create();

        $data = [
            'id' => $translation->id,
            'context' => 'common',
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertFalse($validator->fails());
    }
}
