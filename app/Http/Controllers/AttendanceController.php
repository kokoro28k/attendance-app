<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // 勤怠登録
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

    // 勤怠一覧画面の表示
    public function index(Request $request)
    {
        $yearMonth = $request->input('year_month', Carbon::now()->format('Y-m'));
        $targetDate = Carbon::parse($yearMonth . '-01');

        $prevMonth = $targetDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $targetDate->copy()->addMonth()->format('Y-m');

        // 月初め、月末を作る
        $start = $targetDate->copy()->startOfMonth();
        $end = $targetDate->copy()->endOfMonth();

        // １ヶ月の日付けを作る
        $dates = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()){
            $dates[] = $date->copy();
        }

        // 勤怠データを取得する　キーを日付にする
        $attendances = Attendance::where('user_id',auth()->id())->whereBetween('date', [$start,$end])->with('breakTimes')->get()->keyBy('date');

        // 日付ことに勤怠または、勤怠なしを作る
        $rows = [];
        foreach ($dates as $date){
            $dateKey = $date->format('Y-m-d');
            $rows[] = [
                'date' => $date,
                'attendance' => $attendances[$dateKey] ?? null,
            ];
            }

        return view('user.attendances.index',compact('rows','targetDate','prevMonth','nextMonth'));
    }

    // 勤怠詳細画面の表示
    public function show($id)
    {
        // 本人の勤怠データを取得
        $attendance = Attendance::where('id',$id)->where('user_id',auth()->id())->with('breakTImes')->firstOrFail();

        $year = $attendance->date->format('Y年');
        $monthDay = $attendance->date->format('n月j日');

        $isPending = Application::where('attendance_id',$attendance->id)->where('status','pending')->exists();

        return view('user.attendaces.show',compact('attendace','year','monthDay','isPending'));
    }
}
