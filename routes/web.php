<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AdminApplicationController;

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
Route::get('/admin/login',[AdminLoginController::class,'create'])->name('admin.login');

Route::get('/admin/attendance/list',[AdminAttendanceController::class,'index'])->name('admin.attendance.index');

Route::get('/admin/staff/list',[AdminAttendanceController::class,'staffIndex'])->name('staff.index');
Route::get('/admin/attendance/staff/{id}',[AdminAttendanceController::class,'staffAttendanceIndex'])->name('staff.attendance');

Route::get('/admin/attendance/{id}',[AdminAttendanceController::class,'show'])->name('admin.attendance.show');

Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}',[AdminApplicationController::class,'showApprove'])->name('admin.application.approve.show');

// Route::middleware('auth','role:admin')->group(function(){
   // Route::get('/stamp_correction_request/list',[AdminApplicationController::class,'index'])->name('admin.application.list');
// }); 


// 一般ユーザー
Route::get('/register',[RegisterController::class,'create'])->name('user.register');
Route::get('/login',[LoginController::class,'create'])->name('user.login');

Route::get('/attendance',[AttendanceController::class,'create'])->name('user.attendance.create');
Route::post('/attendance/start',[AttendanceController::class,'start'])->name('user.attendance.start');
Route::post('/attendance/end',[AttendanceController::class,'end'])->name('user.attendance.end');
Route::post('/attendance/break-start',[AttendanceController::class,'breakS</S>tart'])->name('user.break.start');
Route::post('/attendance/break-end',[AttendanceController::class,'breakEnd'])->name('user.break.end');

Route::get('/attendance/list',[AttendanceController::class,'index'])->name('user.attendance.index');
Route::get('/attendance/detail/{id}',[AttendanceController::class,'show'])->name('user.attendance.show');

Route::get('/stamp_correction_request/list',[ApplicationController::class,'index'])->name('user.application.index');


