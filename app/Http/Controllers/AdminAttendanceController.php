<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Application;
use App\Models\BreakTime;
use App\Models\ApplicationBreak;
use App\Http\Requests\AttendanceUpdateRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminAttendanceController extends Controller
{
    // 勤怠一覧画面の表示
    public function index(Request $request)
    {
        $targetDateString = $request->input('date', Carbon::today()->toDateString());

        $targetDate = Carbon::parse($targetDateString);

        $prevDate = $targetDate->copy()->subDay()->toDateString();
        $nextDate = $targetDate->copy()->addDay()->toDateString();

        $attendances = Attendance::with(['user','breakTimes'])
            ->whereDate('date', $targetDateString)
            ->orderBy('user_id')
            ->get();

        return view('admin.attendances.index',[
            'targetDate' => $targetDateString,
            'attendances' => $attendances,
            'prevDate' => $prevDate,
            'nextDate' => $nextDate,
        ]);
    }

    // 勤怠詳細画面の表示
    public function show($id)
    {
        $attendance = Attendance::with(['user','breakTimes'])->findOrFail($id);

        $year = $attendance->date->format('Y年');

        $monthDay = $attendance->date->format('n月j日');

        $application = Application::where('attendance_id',$attendance->id)
        ->latest()
        ->first();

        $isPending = $application && $application->status === Application::STATUS_PENDING;

        // 出勤・退勤（表示用）
        $displayWorkStart = $isPending ? Carbon::parse($application->corrected_work_start)->format('H:i') : optional($attendance->work_start)->format('H:i');

        $displayWorkEnd = $isPending ? Carbon::parse($application->corrected_work_end)->format('H:i') : optional($attendance->work_end)->format('H:i');
   
        // 休憩（表示用）
        $displayBreaks = [];

        $max = max(
            $attendance->breakTimes->count(),
            optional($application?->applicationBreaks)->count() ?? 0
        );

        for ($i = 0; $i < $max; $i ++) {

            $break = $attendance->breakTimes[$i] ?? null;
            $appBreak = $application->applicationBreaks[$i] ?? null;
            
            $displayBreaks[$i] = [
                'start' => $appBreak?->corrected_break_start ? Carbon::parse($appBreak->corrected_break_start)->format('H:i') : optional($break->break_start)->format('H:i'),

                'end' => $appBreak?->corrected_break_end ? Carbon::parse($appBreak->corrected_break_end)->format('H:i') :optional($break->break_end)->format('H:i'),
            ];
        }
        
        $displayReason = $isPending ? $application->reason : '';

        return view('admin.attendances.show',compact('attendance','year','monthDay','isPending','displayWorkStart','displayWorkEnd','displayBreaks','displayReason'));
    }

    public function update(AttendanceUpdateRequest $request,$attendanceId)
    {
        $validated = $request->validated();

        $attendance = Attendance::findOrFail($attendanceId);

        // 勤怠の更新
        $attendance->update([
            'work_start' => $validated['work_start'],
            'work_end' => $validated['work_end'],
            'note' => $validated['note'],
            'status' => Attendance::STATUS_FINISHED,
        ]);

        // 既存の休憩の更新
        foreach($attendance->breakTimes as $index => $break) {
            $break->update([
                'break_start' => $validated['break_start'][$index] ?? null,
                'break_end' => $validated['break_end'][$index] ?? null,
            ]);
        }

        // 空欄の休憩に入力
        $existingCount = $attendance->breakTimes->count();

        if (!empty($validated['break_start'][$existingCount])) {
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => $validated['break_start'][$existingCount] ?? null,
                'break_end' => $validated['break_end'][$existingCount] ?? null,
            ]);
        }
        
        return redirect()->route('admin.attendance.index');
    }

    public function staffIndex()
    {
        $users = User::where('role',User::ROLE_USER)->get();

        return view('admin.staff.index',compact('users'));
    }

    public function staffAttendanceIndex(Request $request, $id)
    {
        $user = User::where('role', User::ROLE_USER)->findOrFail($id);

        $yearMonth = $request->input('year_month');

        if (!$yearMonth) {
            $yearMonth = Carbon::now()->format('Y-m');
        }

        $targetDate = Carbon::createFromFormat('Y-m', $yearMonth);

        $prevMonth = $targetDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $targetDate->copy()->addMonth()->format('Y-m');

        // 月初め、月末を作る
        $start = $targetDate->copy()->startOfMonth();
        $end = $targetDate->copy()->endOfMonth();

        // 月初～月末の勤怠の外枠を自動生成
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            Attendance::firstOrCreate([
                'user_id' => $id,
                'date' => $date->format('Y-m-d'),
            ],
            [
                'status' => Attendance::STATUS_OFF,
            ]
            );
        }

        // 勤怠データを取得する　
        $attendances = Attendance::where('user_id',$id)
            ->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->with('breakTimes')
            ->get()
            ->keyBy(function ($item) {
                return $item->getRawOriginal('date');
            });

        $today = Carbon::today();

        // ユーザーごとに rows　を作る
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

        return view('admin.staff.attendance-index',compact('rows','targetDate','prevMonth','nextMonth','user'));
    }

    public function export($id, Request $request)
    {
        $yearMonth = $request->input('year_month');
        $targetDate = Carbon::parse($yearMonth . '-01');

        $start = $targetDate->copy()->startOfMonth();
        $end = $targetDate->copy()->endOfMonth();

        $attendances = Attendance::where('user_id', $id)
            ->whereBetween('date',[$start, $end])
            ->with('breakTimes')
            ->orderBy('date')
            ->get();

        $csvHeader = [
            '日付', '出勤', '退勤', '休憩', '合計'
        ];

        return new StreamedResponse(function () use ($csvHeader, $attendances) {
            $file = fopen('php://output', 'w');

            $header = array_map(fn($v) => mb_convert_encoding($v, 'SJIS-win', 'UTF-8'),$csvHeader);

            fputcsv($file,$csvHeader);

            foreach ($attendances as $attendance) {
  
                // 休憩(存在しないところは空欄)
                $breakHm = $attendance->break_total_hm;
                $breakHm = ($breakHm === '00:00') ? '' : $breakHm;
                
                // 勤務時間の合計
                $workMinutes = $attendance->work_minutes;
                $workHm = $workMinutes ? sprintf('%02d:%02d', intdiv($workMinutes, 60), $workMinutes % 60) :'';

                fputcsv($file, [
                   $attendance->date->format('Y-m-d'),
                   $attendance->work_start?->format('H:i') ?? '',
                   $attendance->work_end?->format('H:i') ?? '',
                   $breakHm,
                   $workHm,
                ]);
            }

            fclose($file);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment;filename="attendance.csv"',
        ]);
    }
}