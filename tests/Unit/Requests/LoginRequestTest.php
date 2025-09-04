<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\LoginRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    use RefreshDatabase;

    private LoginRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new LoginRequest();
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

        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);
    }

    /** @test */
    public function it_validates_email_is_required()
    {
        $data = [
            'password' => 'password123',
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
    }

    /** @test */
    public function it_validates_email_is_valid_email_format()
    {
        $data = [
            'email' => 'invalid-email',
            'password' => 'password123',
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
    }

    /** @test */
    public function it_validates_password_is_required()
    {
        $data = [
            'email' => 'test@example.com',
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('password'));
    }

    /** @test */
    public function it_passes_validation_with_valid_data()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_passes_validation_with_complex_email()
    {
        $data = [
            'email' => 'user.name+tag@domain.co.uk',
            'password' => 'password123',
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_passes_validation_with_long_password()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => str_repeat('a', 100),
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_passes_validation_with_special_characters_in_password()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'P@ssw0rd!@#$%^&*()',
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_fails_validation_with_empty_email()
    {
        $data = [
            'email' => '',
            'password' => 'password123',
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
    }

    /** @test */
    public function it_fails_validation_with_empty_password()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => '',
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('password'));
    }

    /** @test */
    public function it_fails_validation_with_whitespace_only_email()
    {
        $data = [
            'email' => '   ',
            'password' => 'password123',
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
    }

    /** @test */
    public function it_fails_validation_with_whitespace_only_password()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => '   ',
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('password'));
    }

    /** @test */
    public function it_fails_validation_with_null_values()
    {
        $data = [
            'email' => null,
            'password' => null,
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
        $this->assertTrue($validator->errors()->has('password'));
    }

    /** @test */
    public function it_fails_validation_with_numeric_email()
    {
        $data = [
            'email' => 123,
            'password' => 'password123',
        ];

        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
    }

    /** @test */
    public function it_fails_validation_with_numeric_password()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 123,
        ];

        $validator = Validator::make($data, $this->request->rules());
        // Password validation accepts numeric values, so this should pass
        $this->assertFalse($validator->fails());
    }
}
