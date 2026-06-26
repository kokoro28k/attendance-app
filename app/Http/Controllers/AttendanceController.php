<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\Application;
use App\Models\ApplicationBreak;
use App\Http\Requests\ApplicationRequest;

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
        $today = Carbon::today()->toDateString();

        // 今日の勤怠がすでにあるなら出勤済み扱い
        $attendance = Attendance::where('user_id',auth()->id())
            ->whereDate('date', $today)
            ->first();
        
        if ($attendance) {
            if ($attendance->status !== Attendance::STATUS_OFF) {
            return redirect()->route('user.attendance.create');
            }
        
        // 勤務外なら、出勤に更新する
            $attendance->status = Attendance::STATUS_WORKING;
            $attendance->work_start = Carbon::now();
            $attendance->save();

            return redirect()->route('user.attendance.create');
        } 

        // レコードがない場合は、新規作成する
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
        $today = Carbon::today()->toDateString();

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
        $today = Carbon::today()->toDateString();

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
        $today = Carbon::today()->toDateString();

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

        // 月初～月末の勤怠の外枠を自動生成
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            Attendance::firstOrCreate([
                'user_id' => auth()->id(),
                'date' => $date->format('Y-m-d'),
            ],
            [
                'status' => Attendance::STATUS_OFF,
            ]
            );
        }

        // 勤怠データを取得する　キーを日付にする
        $attendances = Attendance::where('user_id',auth()->id())
            ->whereBetween('date', [$start->format('Y-m-d'),$end->format('Y-m-d')])
            ->with('breakTimes')
            ->get()
            ->keyBy(function ($item) {
                return $item->getRawOriginal('date');
            });

        // 日付ことに勤怠または、勤怠なしを作る
        $today = Carbon::today();
        $rows = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            $attendance = $attendances[$dateKey] ?? null;

            // 表示用のattendance　未来と未出勤はnullにする
            $displayAttendance = ( $date->isFuture() || !$attendance?->work_start ) ? null : $attendance;

            $rows[] = [
                'date' => $dateKey,
                'attendance' => $displayAttendance,
                'attendance_id' => $attendance?->id
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
            ->with('breakTimes')->firstOrFail();

        $year = $attendance->date->format('Y年');
        $monthDay = $attendance->date->format('n月j日');

        // 申請一覧の詳細ボタンを押した場合の表示
        $applicationId = request()->application_id;

        if ($applicationId) {
            $application = Application::where('id', $applicationId)
                ->where('attendance_id',$attendance->id)
                ->with('applicationBreaks')
                ->firstOrFail();
        } else {
            // 通常の勤怠詳細
            $application = Application::where('attendance_id', $attendance->id)
                ->orderByRaw("status = 'pending' DESC")
                ->orderBy('id', 'desc' )
                ->with('applicationBreaks')
                ->first();
        }

        $isPending = $application && $application->status === Application::STATUS_PENDING;

        // 出勤・退勤（表示用）
        $displayWorkStart = $isPending ? Carbon::parse($application->corrected_work_start)->format('H:i')
        : optional($attendance->work_start)->format('H:i');

        $displayWorkEnd = $isPending ? Carbon::parse($application->corrected_work_end)->format('H:i')
        : optional($attendance->work_end)->format('H:i');


        // 休憩（表示用）
        $displayBreaks = [];

        // 申請一覧からの遷移、または申請が承認待ち
        if ($applicationId || $isPending) {
            // 申請内容を表示
            foreach ($application->applicationBreaks as $i => $appBreak) {
                $displayBreaks[$i] = [
                    'start' => $appBreak->corrected_break_start ? Carbon::parse($appBreak->corrected_break_start)->format('H:i') : null,

                    'end' => $appBreak->corrected_break_end ? Carbon::parse($appBreak->corrected_break_end)->format('H:i') : null,
                ];
            }
        } else {

            // 通常の勤怠表示
            foreach ($attendance->breakTimes as $i => $break) {

                $displayBreaks[$i] = [
                    'start' => optional($break->break_start)->format('H:i'),

                    'end' => optional($break->break_end)->format('H:i'),
                ];
            }
        }

        $displayReason = $isPending ? $application->reason : '';

        return view('user.attendances.show',compact('attendance','year','monthDay','isPending','displayWorkStart','displayWorkEnd','displayBreaks','displayReason'));
    }

    public function store(ApplicationRequest $request)
    {
        $validated = $request->validated();
        
        // attedanceを取得
        $attendance = Attendance::find($validated['attendance_id']);

        DB::transaction(function () use ($validated,$attendance) {

            // applicationを作成
            $application = new Application();
            $application->user_id = auth()->id();
            $application->attendance_id = $attendance->id;
            $application->corrected_work_start = $validated['corrected_work_start'] ?? null;
            $application->corrected_work_end = $validated['corrected_work_end'] ?? null;
            $application->reason = $validated['reason'];
            $application->status = Application::STATUS_PENDING;
            $application->applied_at = now()->toDateString();

            $application->save();

            // 休憩の保存
            foreach ($validated['corrected_break_start'] as $i => $start) {
                $end = $validated['corrected_break_end'][$i] ?? null;

                if ($start && $end) {
                    ApplicationBreak::create([
                        'application_id' => $application->id,
                        'corrected_break_start' => $start,
                        'corrected_break_end' => $end,
                    ]);
                }
            }
        });

        return redirect()->route('user.attendance.show', $attendance->id);
    }
}
