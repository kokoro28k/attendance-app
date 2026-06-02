<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class CreateDailyAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:create-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '毎日全ユーザーの勤怠レコード（勤務外）を作成する';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');

        $users = User::where('role',User::ROLE_USER)->get();

        foreach ($users as $user) {
            Attendance::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'date' => $today,
                ],
                [
                    'status' => Attendance::STATUS_OFF,
                ],
            );
        }

        $this->info('Daily attendance records created successfully.');
        return 0;
    }
}
