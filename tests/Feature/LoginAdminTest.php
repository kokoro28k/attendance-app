<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginAdminTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
     public function test_admin_email_is_requires()
    {
        $response = $this->post('/login',[
            'email' => '',
            'password' => 'password',        
            ]);

        $response->assertSessionHasErrors(['email']);
    }

     public function test_admin_password_is_required()
    {
         $response = $this->post('/login',[
            'email' => 'testcase@example.com',
            'password' => '',
            'password_confirmation' => '',
            ]);

        $response->assertSessionHasErrors(['password']);
    }

     public function test_admin_login_fails_with_wrong_password()
    {
        // 新しいユーザーを作成
        $user = User::factory()->create([
            'email' => 'testcase1@example.com',
            'password' => bcrypt('password123'),
        ]);

        // パスワードだけ間違えてログインを試す
         $response = $this->post('/login',[
            'email' => 'testcase1@example.com',
            'password' => 'wrongpassword'
            ]);

        // 認証失敗時は、emailにエラーが入る
        $response->assertSessionHasErrors(['email']);
        
        // ログインしていないことを確認　認証されていない
        $this->assertGuest();
    }
}
