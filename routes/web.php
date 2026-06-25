<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AdminApplicationController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApplicationRedirectController;
use Laravel\Fortify\Http\Controllers\VerifyEmailController;
use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// 管理者
Route::get('/admin/login',[AdminLoginController::class,'create'])
    ->middleware('guest')
    ->name('admin.login');

Route::post('/admin/login',[AdminLoginController::class,'store'])
    ->name('admin.login.store');

Route::post('/admin/logout',[AdminLoginController::class,'destroy'])
    ->name('admin.logout');

// 管理者ログイン済みのみアクセス可能
Route::middleware(['auth:admin','admin'])->group(function () {
    
    // スタッフ一覧
    Route::get('/admin/staff/list',[AdminAttendanceController::class,'staffIndex'])
        ->name('staff.index');

    // 勤怠一覧
    Route::get('/admin/attendance/list',[AdminAttendanceController::class,'index'])
        ->name('admin.attendance.index');

    // スタッフ別勤怠一覧
    Route::get('/admin/attendance/staff/{id}',[AdminAttendanceController::class,'staffAttendanceIndex'])
        ->name('staff.attendance');

    // 勤怠詳細
    Route::get('/admin/attendance/{id}',[AdminAttendanceController::class,'show'])
        ->name('admin.attendance.show');

    //　勤怠詳細修正
    Route::put('/admin/attendance/{id}',[AdminAttendanceController::class,'update'
    ])
        ->name('admin.attendance.update');

    // CSV出力
    Route::get('/admin/attendance/staff/{id}/export',[AdminAttendanceController::class,'export'])
        ->name('staff.attendance.export');

    // 修正申請承認
    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}',[AdminApplicationController::class,'showApprove'])        
        ->name('admin.application.approve.show');    

    // 承認ボタン
    Route::post('/stamp_correction_request/approve/{attendance_correct_request_id}',[AdminApplicationController::class,'approve'])
        ->name('admin.application.approve');
});

// 申請一覧画面
Route::middleware(['auth:admin,web'])->group(function () {
    Route::get('/stamp_correction_request/list',[ApplicationRedirectController::class,'index'])
        ->name('application.list');
});

// 一般ユーザー
Route::middleware('guest')->group(function () {
    Route::get('/register',[RegisterController::class,'create'])
        ->name('user.register');

    Route::post('/register',[RegisterController::class,'store'])
        ->name('register');

    Route::get('/login',[LoginController::class,'create'])
        ->name('user.login');

    Route::post('/login',[LoginController::class,'store'])
        ->name('login');
});

Route::get('/email/verify',function(){
    return view('user.auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}',   
    [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

Route::post('/email/verification-notification', 
    [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');

Route::middleware(['auth','user'])->group(function () {

    // ログアウト
    Route::post('/logout',[LoginController::class,'destroy'])
        ->name('logout');

    Route::middleware('verified')->group(function () {

        // 勤怠登録画面
        Route::get('/attendance',[AttendanceController::class,'create'])
            ->name('user.attendance.create');

        // 勤怠登録画面 出勤処理
        Route::post('/attendance/start',[AttendanceController::class,'start'])
            ->name('user.attendance.start');
    
        // 勤怠登録画面　退勤処理    
        Route::post('/attendance/end',[AttendanceController::class,'end'])
            ->name('user.attendance.end');

        // 勤怠登録画面　休憩入り処理    
        Route::post('/attendance/break/start',[AttendanceController::class,'startBreak'])
            ->name('user.break.start');

        // 勤怠登録画面　休憩戻り処理    
        Route::post('/attendance/break/end',[AttendanceController::class,'endBreak'])
            ->name('user.break.end');

        // 修正申請
        Route::post('/applications',[AttendanceController::class,'store'])
            ->name('user.application.store');

        // 勤怠一覧
        Route::get('/attendance/list',[AttendanceController::class,'index'])
            ->name('user.attendance.index');

        // 勤怠詳細    
        Route::get('/attendance/detail/{id}',[AttendanceController::class,'show'])
            ->name('user.attendance.show');
    });

});
