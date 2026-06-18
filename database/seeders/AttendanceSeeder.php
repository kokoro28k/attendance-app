<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\User;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $base = Carbon::today();
        $prev = $base->copy()->subMonth();
        $now = $base->copy();

        //UsersSeederで作ったユーザー
        $user1 = User::where('email','reina.n@coachtech.com')->first();
        $user2 = User::where('email','taro.y@coachtech.com')->first();

        // user1
        $this->generateMonth($prev, $user1->id, 'patternA',$base);
        $this->generateMonth($now, $user1->id, 'patternA',$base);

        // user2
        $this->generateMonth($prev, $user2->id, 'patternB',$base);
        $this->generateMonth($now, $user2->id, 'patternB',$base);

         // 前月、今月、翌月
        $months = [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->startOfMonth(),
            Carbon::now()->addMonth()->startOfMonth(),
        ];    

        foreach ([$user1->id, $user2->id] as $userId) {
            foreach ($months as $monthStart) {

            $start = $monthStart->copy();
            $end = $monthStart->copy()->endOfMonth();

                for ($date = $start->copy(); $date->lte($end); $date->addDay()) {

                    Attendance::firstOrCreate(
                        [
                            'user_id' => $userId,
                            'date' => $date->format('Y-m-d'),
                        ],
                        [
                            'status' => Attendance::STATUS_OFF
                        ]
                    );
                }
            }
        }
    }

    private function generateMonth(Carbon $carbon, int $userId, string $pattern, Carbon $base)
    {
        $start = $carbon->copy()->startOfMonth();
        $yesterdayStr = $base->copy()->subDay()->format('Y-m-d');
        $endOfMonth = $carbon->copy()->endOfMonth();

        $date = $start->copy();

        while ($date->lte($endOfMonth) && $date->format('Y-m-d') <= $yesterdayStr) {

            $dayOfWeek = $date->dayOfWeekIso;
            $workStart = null;
            $workEnd = null;
            $status = Attendance::STATUS_OFF;

            // 月曜日～金曜日だけ
            if ($pattern === 'patternA' && $dayOfWeek <=5) {
                $workStart = '09:00:00';
                $workEnd = '18:00:00';
                $status = Attendance::STATUS_FINISHED;
            }
            
            // 月・水・金だけ勤務
            if ($pattern === 'patternB' && in_array($dayOfWeek, [1,3,5])) {
            
                $workStart = '10:00:00';
                $workEnd = '17:00:00';
                $status = Attendance::STATUS_FINISHED;
            }
            
                Attendance::updateOrCreate(
                [
                    'user_id' => $userId,
                    'date' => $date->format('Y-m-d'),
                ],
                [
                    'work_start' => $workStart,
                    'work_end' => $workEnd,
                    'status' => $status,
                ]);
            $date->addDay();
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

