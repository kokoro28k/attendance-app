<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Application;

class AdminApplicationController extends Controller
{
    // 申請一覧画面の表示
    public function index(Request $request)
    {
        $tab = $request->query('tab','pending');
     
        $query = Application::with(['user','attendance']);

        if ($tab === 'pending') {
            $query->where('status', Application::STATUS_PENDING)->orderBy('applied_at','asc');
        } elseif ($tab === 'approved') {
            $query->where('status', Application::STATUS_APPROVED)->orderBy('applied_at','desc');
        }

        $applications = $query->get();

        return view('admin.applications.index',compact('applications','tab'));
    }

    // 修正承認画面の表示
    public function showApprove($applicationId)
    {
        $application = Application::with(['user','attendance','attendance.breakTimes','applicationBreaks'])->findOrFail($applicationId);

        $isPending = $application->status === Application::STATUS_PENDING;

        return view('admin.applications.approve',compact('application', 'isPending'));
    }

    public function approve($id)
    {
        $application = Application::with([
            'attendance.breakTimes',
            'applicationBreaks'
        ])->findOrFail($id);

        $application->status = Application::STATUS_APPROVED;
        $application->save();

        $attendance = $application->attendance;

        $attendance->work_start = $application->corrected_work_start ?? $attendance->work_start;
        $attendance->work_end = $application->corrected_work_end ?? $attendance->work_end;
        $attendance->save();

        foreach ($application->applicationBreaks as $index => $applicationBreak) {

            // 元の休憩を順番に取得
            $breakTime = $attendance->breakTimes[$index] ?? null;
  
            if ($breakTime) {
                $breakTime->break_start = $applicationBreak->corrected_break_start ?? $breakTime->break_start;
                $breakTime->break_end = $applicationBreak->corrected_break_end ?? $breakTime->break_end;
                $breakTime->save();
            }
        }
        return back();
    }
    
}
