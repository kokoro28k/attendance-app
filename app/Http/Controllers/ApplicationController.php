<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;


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

    public function showAttendance($applicationId)
    {
        // 途中までしか書いていない
        $application = Application::with(['attendance','attendance.breakTimes','applicationBreaks'])->where('id',$id)->where('user_id',auth()->id())->findOrFail();

        return view('user.attendances.show',compact('application'));

    }
}
