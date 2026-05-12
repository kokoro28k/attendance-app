<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function create()
    {
        Carbon::setlocale('ja');

        $now = Carbon::now();

        $formattedDate = $now->translatedFormat('Y年m月d日(D)');
        $formattedTime = $now->format('H:i');

        $attendance = Attendance::where('user_id',auth()->id())->whereDate('date',Carbon::today())->first();

        return view('user.attendances.create',[
            'formattedDate' => $formattedDate,
            'formattedTime' => $formattedTime,
            'attendance' => $attendance,
        ]);
    }

    public function index(Request $request)
    {
        $yearMonth = $request->input('year_month', Carbon::now()->format('Y-m'));

        $targetDate = Carbon::parse($yearMonth . '-01');

        $prevMonth = $targetDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $targetDate->copy()->addMonth()->format('Y-m');

        $attendances = Attendance::where('user_id',auth()->id())->whereMonth('date', $targetDate->month)->with('breakTimes')->get();

        $attendance = Attendance::where('user_id',auth()->id())->whereDate('date',Carbon::today())->first();

        return view('user.attendances.index',compact('attendances','targetDate','prevMonth','nextMonth','attendance'));
    }
}
