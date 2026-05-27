<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // ログアウトはミドルウェアを通す
        if ($request->routeIs('admin.logout')) {
        return $next($request);
        }

        // まだログインしていない場合はログイン画面へ遷移
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        // ログインしているがroleがadminではない
        if (Auth::guard('admin')->user()->role !=='admin') {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
