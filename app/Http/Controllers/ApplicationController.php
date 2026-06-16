<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Application;
use App\Models\Attendance;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab','pending');
     
        $query = Application::with(['attendance','user'])->where('user_id',auth()->id());

        if ($tab === 'pending') {
            $query->where('status',Application::STATUS_PENDING)->orderBy('applied_at','asc');
        } elseif ($tab === 'approved') {
            $query->where('status',Application::STATUS_APPROVED)->orderBy('applied_at','desc');
        }

        $applications = $query->orderBy('user_id')->get();

        return view('user.applications.index',compact('applications','tab'));
    }

    // 申請一覧画面の詳細ボタンの処理
    public function showAttendance($applicationId)
    {
        $application = Application::where('id',$applicationId)->where('user_id',auth()->id())->findOrFail();

        return redirect()->route('user.attendance.show',$application->attendance_id);
    }
}
