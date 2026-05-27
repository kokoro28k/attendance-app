<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {

            // 管理者ページにアクセスした場合
            if ($request->is('admin/*')) {
                return route('admin.login');
            }

            // それ以外は一般ユーザーのログインへ
            return route('login');
            }
         return null;
    }
}