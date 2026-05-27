<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\User;
use App\Http\Request\AttendanceRequest;

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

        // 月初め、月末を作る
        $start = $targetDate->copy()->startOfMonth();
        $end = $targetDate->copy()->endOfMonth();

        // １ヶ月の日付けを作る
        $dates = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()){
            $dates[] = $date->copy();
        }

        // 勤怠データを取得する　
        $attendances = Attendance::with('breakTimes')
            ->where('user_id',$id)
            ->whereBetween('date', [$start, $end])
            ->get()
            ->keyBy('date');

        // ユーザーごとに rows　を作る
        $rows = [];
        foreach ($dates as $date) {
            $dateKey = $date->format('Y-m-d');
            $rows[$userId][] = [
                'date' => $date,
                'attendance' => $userAttendances[$dateKey] ?? null,
            ];
        }

        return view('admin.staff.attendance-index',compact('rows','targetDate','prevMonth','nextMonth','attendances','user'));
    }

    public function update(AttendanceRequest $request,$id)
    {
        $attendance = Attendance::with('breakTimes')->findOrFail($id);

        $validated = $request->validated();

        $attendance->update([
            'work_start' => $validated['work_start'],
            'work_end' => $validated['work_end'],
            'reason' => $validated['reason'],
        ]);

        foreach ($attendance->breakTimes as $index => $break) {
            $break->update([
                'break_start' => $validated['break_start'][$index],
                'break_end' => $validated['break_end'][$index],
            ]);
        }

        // 日別勤怠一覧にリダイレクトする？（要確認）
        return redirect()->route('admin.attendance.index');
    }
}
