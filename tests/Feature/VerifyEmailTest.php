<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;

class VerifyEmailTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_registration_sends_verification_email()
    {
        Notification::fake();

        $userData=[
            'name' => 'テスト太郎',
            'email' => 'testcase@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
    
        // 会員登録をする
        $response = $this->post(route('register'),$userData);

        $this->assertDatabaseHas('users',[
            'email' => 'testcase@example.com',
        ]);

        // 登録されたユーザーを取得する
        $user = User::where('email','testcase@example.com')->first();

        // 認証メールが送信されていること
        Notification::assertSentTo($user,VerifyEmail::class);
    }

    
    public function test_user_is_redirected_to_email_verify_page()
    {
        $user = User::factory()->unverified()->create();
    
        $response =$this->actingAs($user)->get('/email/verify');

        $response->assertStatus(200);
        $response->assertSee('認証はこちらから');
  
        // ボタンを押下してアクセスする
        $response =$this->actingAs($user)->get('/email/verify');

        // 同じ画面が表示される
        $response->assertStatus(200);
        $response->assertSee('認証はこちらから');  
      
    }

    public function test_user_is_redirected_to_profile_after_email_verification()
    {  
        $user = User::factory()->unverified()->create();
    
        // 認証リンク（署名付きURL)を作成する
        $url = URL::temporarySignedRoute('verification.verify',now()->addMinutes(60),
        [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        // 認証リンクにアクセスする（ログイン状態で)
        $response = $this->actingAs($user)->get($url);

        // 勤怠登録画面に遷移する
        $response->assertStatus(302);
        $response->assertRedirect(route('user.attendance.create'));  
      
        // ユーザーが認証済みになっていることを確認する
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
