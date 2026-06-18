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
        $userSettings = [
            [
                'email' =>  'reina.n@coachtech.com',
            ],
            [
                'email' => 'taro.y@coachtech.com',
            ],
        ];

        foreach ($userSettings as $setting) {

            $user = User::where('email', $setting['email'])->first();

            $baseMonday = Carbon::now()->subWeek()->startOfWeek();
  
            // 承認済み
            $approvedDate = $baseMonday->copy();

            $approvedAttendance = Attendance::where('user_id', $user->id)
                ->where('date', $approvedDate->toDateString())
                ->first();

             if ($approvedAttendance) {
                $approved = Application::firstOrCreate(
                    [
                        'attendance_id' => $approvedAttendance->id,
                        'status' => Application::STATUS_APPROVED,
                    ],
                    [
                        'user_id' => $user->id,
                        'corrected_work_start' => '09:00',
                        'corrected_work_end' => '17:30', 
                        'reason' => '勤怠登録忘れのため',
                        'applied_at' => $approvedDate->copy()->addDay(),
                    ]
                );
        
                ApplicationBreak::firstOrCreate(
                    [
                        'application_id' => $approved->id,
                        'corrected_break_start' => '12:00',
                        'corrected_break_end' => '13:00',
                    ]
                );
            }

            // 承認待ち
            $pendingDate = $baseMonday->copy()->addDays(2);

            $pendingAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $pendingDate->toDateString())
            ->first();

            if ($pendingAttendance) {
                $pending = Application::firstOrCreate(
                    [
                        'attendance_id' => $pendingAttendance->id,
                        'status' => Application::STATUS_PENDING,
                    ],
                    [
                        'user_id' => $user->id,
                        'corrected_work_start' => '09:30',
                        'corrected_work_end' => '18:00',
                        'reason' => '勤怠登録忘れのため',
                        'applied_at' => $pendingDate->copy()->addDay(),
                    ]
                );

                ApplicationBreak::firstOrCreate(
                    [
                        'application_id' => $pending->id,
                        'corrected_break_start' => '12:00',
                        'corrected_break_end' => '13:00',
                    ]
                );
            }
        }
    }
}

