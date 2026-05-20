<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\User;

class AdminAttendanceController extends Controller
{
    // 勤怠一覧画面の表示
    public function index(Request $request)
    {
        $targetDate = Carbon::parse($request->input('date', Carbon::today()));

        $prevDate = $targetDate->copy()->subDay()->format('Y-m-d');
        $nextDate = $targetDate->copy()->addDay()->format('Y-m-d');

        $attendances = Attendance::with(['user','breakTimes'])
            ->whereDate('date', $targetDate)
            ->orderBy('user_id')
            ->get();

        return view('admin.attendances.index',compact('targetDate','attendances','prevDate','nextDate'));
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
}
