<?php

use Illuminate\Support\Facades\Route;

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
Route::get('/admin/attendance/list',[AttendanceController::class,'index'])->name('admin.attendance.index');
Rout::get('/admin/staff/list',[AttendanceController::class,'index'])->name('staff.index');

Route::middleware(['is_admin'])->group(function () {
Route::get('/stamp_correction_request/list',[ApplicationController::class,'index'])->name('admin.application');
});

// 一般ユーザー
Route::get('/attendance',[AttendanceController::class,'create'])->name('attendance.create');
Route::get('/attendance/list',[AttendanceController::class,'index'])->name('user.attendance.index');
Route::get('/stamp_correction_request/list',[ApplicationController,'index'])->name('user.application.index');
