<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Application;

class AdminApplicationController extends Controller
{
    public function index(Request $request)
    {

        $tab = $request->query('tab','pending');
     
        $query = Application::with(['user','attendance']);

        if ($tab === 'pending') {
            $query->where('status','pending')->orderBy('applied_at','asc');
        } elseif ($tab === 'approved') {
            $query->where('status','approved')->ordeBy('applied_at','desc');
        }

        $applications = $query->orderBy('user_id')->get();

        return view('admin.applications.index',compact('applications','tab'));
    }

    public function showApprove($id)
    {
        $application = Application::with(['user','attendance','attendance.breakTimes','applicationBreaks'])->findOrFail($id);

        return view('admin.applications.approve',compact('application'));
    }
}
