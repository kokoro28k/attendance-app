<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\User;

class AttendancesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 前月と今月
        $prev = Carbon::now()->subMonth();
        $now = Carbon::now();

        //UsersSeederで作ったユーザー
        $user1 = User::where('email','reina.n@coachtech.com')->first();
        $user2 = User::where('email','taro.y@coachtech.com')->first();

        // user1
        $this->generateMonth($prev, $user1->id, 'patternA');
        $this->generateMonth($now, $user1->id, 'patternA');

        // user2
        $this->generateMonth($prev, $user2->id, 'patternB');
        $this->generateMonth($now, $user2->id, 'patternB');
    }

    private function generateMonth(Carbon $carbon, int $userId, string $pattern)
    {
        $start = $carbon->copy()->startOfMonth();
        $today = Carbon::yesterday();

        for ($date = $start->copy(); $date->lte($today); $date->addDay()){
            // 月曜日～金曜日だけ
            if ($date->dayOfWeekIso > 5) continue;
            if ($pattern === 'patternA') {
                $workStart = '09:00:00';
                $workEnd = '18:00:00';
            }

            // 月・水・金だけ勤務
            if ($pattern === 'patternB') {
                if ($date->dayOfWeekIso === 2 || $date->dayOfWeekIso === 4) {
                    continue;
                }
                $workStart = '10:00:00';
                $workEnd = '17:00:00';
            }
            
            Attendance::firstOrCreate([
                'user_id' => $userId,
                'date' => $date->format('Y-m-d'),
                'work_start' => $workStart,
                'work_end' => $workEnd,
                'status' => Attendance::STATUS_FINISHED,
            ]);
        }

        // 今日～月末の外枠を作る
        $endOfMonth = $carbon->copy()->endOfMonth();
        $tomorrow = Carbon::today();

        for ($date = $tomorrow->copy(); $date->lte($endOfMonth); $date->addDay()) {
            Attendance::firstOrCreate([
                'user_id' => $userId,
                'date' => $date->format('Y-m-d'),
            ], [
            'status' => Attendance::STATUS_OFF,
            ]);
        }

    }
}

