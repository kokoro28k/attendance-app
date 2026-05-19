<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Attendance;


class ApplicationController extends Controller
{
    public function index(Request $request)
    {

        $tab = $request->query('tab','pending');
     
        $query = Application::with(['attendance'])->where('user_id',auth()->id());

        if ($tab === 'pending') {
            $query->where('status','pending')->orderBy('applied_at','asc');
        } elseif ($tab === 'approved') {
            $query->where('status','approved')->ordeBy('applied_at','desc');
        }

        $applications = $query->orderBy('user_id')->get();

        return view('admin.applications.index',compact('applications','tab'));
    }

    // 申請一覧画面の詳細ボタンの処理
    public function showAttendance($applicationId)
    {
        // 途中までしか書いていない
        $application = Application::where('id',$applicationId)->where('user_id',auth()->id())->findOrFail();

        return redirect()->route('user.attendance.show',$application->attendance_id);

    }

    public function approve()
    {
        $year = $attendace->date->format('Y年');
        $monthDay =  $attendance->date->format('n月j日');
    }

    public function store(Request $request,$id)
    {
        $attendance = Attendance::where('user_id')->get();

        $year = $attendance->date->format();
        $monthDay = $attendance->date->format();

        $index =

        $isPending = Application::where('status',STATUS_PENDING);

    }
}
