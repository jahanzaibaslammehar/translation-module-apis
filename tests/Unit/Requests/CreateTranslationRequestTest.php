<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\CreateTranslationRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CreateTranslationRequestTest extends TestCase
{
    use RefreshDatabase;

    private CreateTranslationRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new CreateTranslationRequest();
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

        $this->assertArrayHasKey('context', $rules);
        $this->assertArrayHasKey('locale', $rules);
        $this->assertArrayHasKey('translations', $rules);
    }

    /** @test */
    public function it_validates_context_is_required()
    {
        $data = [
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
    public function it_validates_context_is_string()
    {
        $data = [
            'context' => 123,
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
    public function it_validates_context_max_length()
    {
        $data = [
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
    public function it_validates_locale_is_required()
    {
        $data = [
            'context' => 'common',
            'translations' => [
                ['key' => 'welcome', 'translation' => 'Welcome'],
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('locale'));
    }

    /** @test */
    public function it_validates_locale_is_string()
    {
        $data = [
            'context' => 'common',
            'locale' => 123,
            'translations' => [
                ['key' => 'welcome', 'translation' => 'Welcome'],
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('locale'));
    }

    /** @test */
    public function it_validates_locale_max_length()
    {
        $data = [
            'context' => 'common',
            'locale' => str_repeat('a', 256),
            'translations' => [
                ['key' => 'welcome', 'translation' => 'Welcome'],
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('locale'));
    }

    /** @test */
    public function it_validates_translations_is_required()
    {
        $data = [
            'context' => 'common',
            'locale' => 'en',
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('translations'));
    }

    /** @test */
    public function it_validates_translations_is_array()
    {
        $data = [
            'context' => 'common',
            'locale' => 'en',
            'translations' => 'not-an-array',
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('translations'));
    }

    /** @test */
    public function it_validates_translation_key_is_required()
    {
        $data = [
            'context' => 'common',
            'locale' => 'en',
            'translations' => [
                '' => 'Welcome',  // Empty key should fail validation
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('translations.'));
    }

    /** @test */
    public function it_validates_translation_key_is_string()
    {
        $data = [
            'context' => 'common',
            'locale' => 'en',
            'translations' => [
                123 => 'Welcome',  // Numeric key should fail validation
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('translations.123'));
    }

    /** @test */
    public function it_validates_translation_value_is_required()
    {
        $data = [
            'context' => 'common',
            'locale' => 'en',
            'translations' => [
                'welcome' => '',  // Empty value should fail validation
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('translations.welcome'));
    }

    /** @test */
    public function it_validates_translation_value_is_string()
    {
        $data = [
            'context' => 'common',
            'locale' => 'en',
            'translations' => [
                'welcome' => 123,  // Numeric value should fail validation
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('translations.welcome'));
    }

    /** @test */
    public function it_validates_translation_value_max_length()
    {
        $data = [
            'context' => 'common',
            'locale' => 'en',
            'translations' => [
                'welcome' => str_repeat('a', 256),  // Value too long should fail validation
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('translations.welcome'));
    }

    /** @test */
    public function it_passes_validation_with_valid_data()
    {
        $data = [
            'context' => 'common',
            'locale' => 'en',
            'translations' => [
                'welcome' => 'Welcome',
                'hello' => 'Hello',
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_multiple_translations()
    {
        $data = [
            'context' => 'common',
            'locale' => 'en',
            'translations' => [
                'welcome' => 'Welcome',
                'hello' => 'Hello',
                'goodbye' => 'Goodbye',
            ],
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertFalse($validator->fails());
    }
}
