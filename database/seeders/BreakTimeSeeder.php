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

            // 勤務していない日は休憩は作らない
            if (is_null($attendance->work_start)){
                continue;
            }

            $breakStart = $attendance->date->copy()->setTime(12, 0);
            $breakEnd = $attendance->date->copy()->setTime(13, 0);

            BreakTime::updateOrCreate(
            [
                'attendance_id' => $attendance->id,
                'break_start' => $breakStart,
            ],
            [   
                'break_end' => $breakEnd,
            ]    
            );
        }

        // user2の勤怠に休憩（2回分）をつける
        $user2 = User::where('email','taro.y@coachtech.com')->first();
        $attendancesUser2 = Attendance::where('user_id', $user2->id)->get();

        foreach ($attendancesUser2 as $attendance) {

            // 勤務していない日は休憩を作らない
            if (is_null($attendance->work_start)){
                continue;
            }

            $breakStart1 = $attendance->date->copy()->setTime(12, 0);
            $breakEnd1 = $attendance->date->copy()->setTime(12, 30);

            BreakTime::updateOrCreate(
            [
                'attendance_id' => $attendance->id,
                'break_start' => $breakStart1
            ],    
            [  
                'break_end' => $breakEnd1
            ]
            );

            $breakStart2 = $attendance->date->copy()->setTime(15, 0);
            $breakEnd2 = $attendance->date->copy()->setTime(15, 30);

            BreakTime::updateOrCreate(
            [
                'attendance_id' => $attendance->id,
                'break_start' => $breakStart2
            ],
            [
                'break_end' => $breakEnd2
            ]
            );
        }
    }
}
