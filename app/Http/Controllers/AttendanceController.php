<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use App\Models\BreakTime;

class AttendanceController extends Controller
{
    // 勤怠登録 画面の表示
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
            'STATUS_OFF' => Attendance::STATUS_OFF,
            'STATUS_WORKING' => Attendance::STATUS_WORKING,
            'STATUS_BREAK' => Attendance::STATUS_BREAK,
            'STATUS_FINISHED' => Attendance::STATUS_FINISHED,
        ]);
    }

    // 勤怠登録 出勤
    public function start(Request $request)
   {
        $today = Carbon::today();

        // 今日の勤怠がすでにあるなら出勤済み扱い
        $attendance = Attendance::where('user_id',auth()->id())
            ->whereDate('date', $today)
            ->first();
        
        if ($attendance) {
            return redirect()->route('user.attendance.create');
        }

        // 出勤レコードを作成
        Attendance::create([
            'user_id' => auth()->id(),
            'date' => $today,
            'work_start' => Carbon::now(),
            'status' => Attendance::STATUS_WORKING,
        ]);

        return redirect()->route('user.attendance.create');
    } 

    // 勤怠登録　休憩入り
    public function startBreak()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date',today())
            ->first();

        // 出勤中ではないなら、休憩入りできない
        if (!$attendance  || $attendance->status !== Attendance::STATUS_WORKING) {
            return back();
        }

        // statusを休憩中に変更
        $attendance->status = Attendance::STATUS_BREAK;
        $attendance->save();

        // Break_timesテーブルに休憩開始を記録
        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::now(),
        ]);

        return redirect()->route('user.attendance.create');
    }

    public function endBreak()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date',today())
            ->first();

        // 休憩中ではないなら、休憩戻りできない
        if (!$attendance  || $attendance->status !== Attendance::STATUS_BREAK) {
            return back();
        }
    
        // 最新の休憩レコードを取得
        $break = BreakTime::where('attendance_id', $attendance->id)
            ->whereNull('break_end')
            ->latest()
            ->first();

        if (!$break) {
            return back();
        }    

        // Break_timesテーブルに休憩終了時刻を記録
        $break->break_end = Carbon::now();
        $break->save();

        // statusを出勤中に戻す
        $attendance->status = Attendance::STATUS_WORKING;
        $attendance->save();

        return redirect()->route('user.attendance.create');
    }

    public function end()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date',today())
            ->first();

        // 出勤中ではないなら、退勤できない
        if (!$attendance  || $attendance->status !== Attendance::STATUS_WORKING) {
            return back();
        }

        // Attendanceテーブルに退勤時刻を記録
        $attendance->work_end = Carbon::now();
        
        // statusを退勤済に変更
        $attendance->status = Attendance::STATUS_FINISHED;
        $attendance->save();
        
        return redirect()->route('user.attendance.create');
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
        $attendances = Attendance::where('user_id',auth()->id())
            ->whereBetween('date', [$start,$end])->with('breakTimes')
            ->get()
            ->keyBy('date');

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
        $attendance = Attendance::where('id',$id)    
            ->where('user_id',auth()->id())
            ->with('breakTImes')->firstOrFail();

        $year = $attendance->date->format('Y年');
        $monthDay = $attendance->date->format('n月j日');

        $isPending = Application::where('attendance_id',$attendance->id)
            ->where('status','pending')
            ->exists();

        return view('user.attendaces.show',compact('attendace','year','monthDay','isPending'));
    }
}
