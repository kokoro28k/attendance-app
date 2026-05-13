<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\User;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $yearMonth = $request->input('year_month', Carbon::now()->format('Y-m'));

        $targetDate = Carbon::parse($yearMonth . '-01');

        $prevMonth = $targetDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $targetDate->copy()->addMonth()->format('Y-m');

        $attendances = Attendance::with(['user','breakTimes'])
        ->whereYear('date', $targetDate->year)->whereMonth('date', $targetDate->month)->orderBy('user_id')->orderBy('date')->get();

        return view('admin.attendances.index',compact('attendances','targetDate','prevMonth','nextMonth'));
    }

    public function show($id)
    {
        $attendance = Attendance::with(['user','breakTimes'])->findOrFail($id);

        return view('admin.attendances.show',compact('attendance'));
    }

    public function staffIndex()
    {
        $users = User::where('role',User::ROLE_USER)->get();

        return view('admin.staff.index',compact('users'));
    }

    public function staffAttendanceIndex(Request $request, $id)
    {

        $user = User::where('role', User::ROLE_USER)->findOrFail($id);

        $yearMonth = $request->input('year_month', Carbon::now()->format('Y-m'));

        $targetDate = Carbon::parse($yearMonth . '-01');

        $prevMonth = $targetDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $targetDate->copy()->addMonth()->format('Y-m');

        $attendances = Attendance::with(['breakTimes'])->where('user_id',$id)->whereYear('date',$targetDate->year)->whereMonth('date', $targetDate->month)->orderBy('date')->get();

        return view('admin.staff.attendance-index',compact('targetDate','prevMonth','nextMonth','attendances','user'));
    }
}
