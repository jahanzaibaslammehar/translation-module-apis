<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = new User();
    }

    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $expectedFillable = ['name', 'email', 'password'];
        
        $this->assertEquals($expectedFillable, $this->user->getFillable());
    }

    /** @test */
    public function it_has_correct_hidden_attributes()
    {
        $expectedHidden = ['password', 'remember_token'];
        
        $this->assertEquals($expectedHidden, $this->user->getHidden());
    }

    /** @test */
    public function it_has_correct_casts()
    {
        $expectedCasts = [
            'id' => 'int',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
        
        $this->assertEquals($expectedCasts, $this->user->getCasts());
    }

    /** @test */
    public function it_can_create_user_with_valid_data()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $user = User::create($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userData['name'], $user->name);
        $this->assertEquals($userData['email'], $user->email);
        $this->assertTrue(Hash::check($userData['password'], $user->password));
    }

    /** @test */
    public function it_can_update_user_attributes()
    {
        $user = User::factory()->create();
        
        $user->update(['name' => 'Jane Doe']);
        
        $this->assertEquals('Jane Doe', $user->fresh()->name);
    }

    /** @test */
    public function it_uses_correct_table_name()
    {
        $this->assertEquals('users', $this->user->getTable());
    }

    /** @test */
    public function it_has_api_tokens_trait()
    {
        $this->assertTrue(method_exists($this->user, 'createToken'));
    }

    /** @test */
    public function it_has_factory_trait()
    {
        $this->assertTrue(method_exists($this->user, 'factory'));
    }

    /** @test */
    public function it_has_notifications_trait()
    {
        $this->assertTrue(method_exists($this->user, 'notify'));
    }
}
