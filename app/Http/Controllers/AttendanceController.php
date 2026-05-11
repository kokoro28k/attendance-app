<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;

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
}
