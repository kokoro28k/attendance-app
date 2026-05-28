<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BreakTime;
use App\Models\Attendance;

class BreakTimesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attendances = Attendance::whereIn('user_id',[2, 3])->get();

        foreach ($attendances as $attendance) {

            // user2
            if ($attendance->user_id === 2) {
                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => '12:00:00',
                    'break_end' => '13:00:00'
                ]);
            }

            // user3　休憩が二回の場合
            if ($attendance->user_id === 3) {

                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => '12:00:00',
                    'break_end' => '12:30:00'
                ]);

                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => '15:00:00',
                    'break_end' => '15:30:00'
                ]);
            }
        }
    }
}
