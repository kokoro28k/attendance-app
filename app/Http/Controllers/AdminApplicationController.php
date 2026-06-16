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
}
