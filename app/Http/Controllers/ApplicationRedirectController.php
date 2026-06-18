<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminApplicationController;
use App\Http\Controllers\ApplicationController;
use App\Models\User;

class ApplicationRedirectController extends Controller
{
    protected $adminController;
    protected $userController;

    public function __construct(
        AdminApplicationController $adminController,
        ApplicationController $userController
    ) {
        $this->adminController = $adminController;
        $this->userController = $userController;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // ログインチェック
        if (!$user) {
            return redirect()->route('user.login');
        }

        // 管理者ログイン
        if ($user->role === User::ROLE_ADMIN) {
            return $this->adminController->index($request);
        }

        // 一般ユーザー
        if ($user->role === User::ROLE_USER) {
            return  $this->userController->index($request);
        }

        abort(403);
    }
}