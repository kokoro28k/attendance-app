<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Application;
use App\Models\ApplicationBreak;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            User::where('email','reina.n@coachtech.com')->first()->id,
            User::where('email','taro.y@coachtech.com')->first()->id,
        ];

        foreach ($users as $userId) {

            // 今日以前の勤怠から最新の一件を取得
            $attendance = Attendance::where('user_id', $userId)
            ->where('date', '<=', Carbon::today()->format('Y-m-d'))
            ->orderBy('date', 'desc')
            ->first();

            if (!$attendance) continue;

            // 承認待ち
            $pending = Application::firstOrCreate(
                [
                    'attendance_id' => $attendance->id,
                    'status' => Application::STATUS_PENDING,
                ],
                [
                    'user_id' => $userId,
                    'corrected_work_start' => '09:30',
                    'corrected_work_end' => '18:00', 
                    'reason' => '勤怠登録忘れのため',
                    'applied_at' => Carbon::now(),
                ]);

            ApplicationBreak::firstOrCreate([
                'application_id' => $pending->id,
                'corrected_break_start' => '12:00',
                'corrected_break_end' => '13:00',
            ]);    

            // 承認済み
            $approved = Application::firstOrCreate(
                [
                    'attendance_id' => $attendance->id,
                    'status'  => Application::STATUS_APPROVED,
                ],
                [
                    'user_id' => $userId,
                    'corrected_work_start' => '09:00',
                    'corrected_work_end' => '17:30',
                    'reason' => '勤怠登録忘れのため',
                    'applied_at' => Carbon::parse($attendance->date)->addDay(),
            ]);

            ApplicationBreak::firstOrCreate([
                'application_id' => $approved->id,
                'corrected_break_start' => '12:00',
                'corrected_break_end' => '13:00',
            ]);
        }
    }
}
