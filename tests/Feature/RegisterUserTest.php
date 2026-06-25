<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
     public function test_name_is_required()
    {
        $response = $this->post('/register',[
            'name' => '',
            'email' => 'testcase@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            ]);

        $response->assertSessionHasErrors(['name']);
    }

     public function test_email_is_required()
    {
        $response = $this->post('/register',[
            'name' => 'テスト太郎',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
            ]);

        $response->assertSessionHasErrors(['email']);
    }

     public function test_password_must_be_at_least_8_characters()
    {
        $response = $this->post('/register',[
            'name' => 'テスト太郎',
            'email' => 'testcase@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
            ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_password_confirmation_must_match()
    {
        $response = $this->post('/register',[
            'name' => 'テスト太郎',
            'email' => 'testcase@example.com',
            'password' => 'password',
            'password_confirmation' => 'password1',
            ]);

        $response->assertSessionHasErrors(['password']);
    }

     public function test_password_is_required()
    {
        $response = $this->post('/register',[
            'name' => 'テスト太郎',
            'email' => 'testcase@example.com',
            'password' => '',
            'password_confirmation' => '',
            ]);

        $response->assertSessionHasErrors(['password']);
    }

     public function test_new_user_can_register()
    {
        $response = $this->post('/register',[
            'name' => 'テスト太郎',
            'email' => 'testcase@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            ]);

        $response->assertRedirect('/email/verify');    

        $this->assertDatabaseHas('users',[
            'email' => 'testcase@example.com',
        ]);

        $this->assertAuthenticated();
    }
}
