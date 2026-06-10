<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BreakTime;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class BreakTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // user1の勤怠に休憩をつける
        $user1 = User::where('email','reina.n@coachtech.com')->first();
        $attendancesUser1 = Attendance::where('user_id',$user1->id)->get();

        foreach ($attendancesUser1 as $attendance) {
            BreakTime::firstOrCreate(
            [
                'attendance_id' => $attendance->id,
                'break_start' => Carbon::parse($attendance->date->format('Y-m-d') . ' 12:00:00'),
            ],
            [   
                'break_end' => Carbon::parse        ($attendance->date->format('Y-m-d') . ' 13:00:00'),
            ]
            );
        }

        // user2の勤怠に休憩（2回分）をつける
        $user2 = User::where('email','taro.y@coachtech.com')->first();
        $attendancesUser2 = Attendance::where('user_id', $user2->id)->get();

        foreach ($attendancesUser2 as $attendance) {
            BreakTime::firstOrCreate(
            [
                'attendance_id' => $attendance->id,
                'break_start' => Carbon::parse($attendance->date->format('Y-m-d') . ' 12:00:00'),
            ],
            [  
                'break_end' => Carbon::parse($attendance->date->format('Y-m-d') . ' 12:30:00')
            ]
            );

            BreakTime::firstOrCreate(
            [
                'attendance_id' => $attendance->id,
                'break_start' => Carbon::parse($attendance->date->format('Y-m-d') . ' 15:00:00'),
            ],
            [
                'break_end' => Carbon::parse($attendance->date->format('Y-m-d') . ' 15:30:00')
            ]
            );
        }
    }
}
