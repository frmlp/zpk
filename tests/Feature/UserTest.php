<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_poprawne_uwierzytelnianie()
    {
        $user = User::factory()->create([
            'name' => 'testuser',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/login', [
            'name' => 'testuser',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    public function test_brak_uwierzytelniania_przy_błędnym_haśle()
    {
        $user = User::factory()->create([
            'name' => 'testuser',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/login', [ 
            'name' => 'testuser',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422); 
        $this->assertGuest();
    }
}