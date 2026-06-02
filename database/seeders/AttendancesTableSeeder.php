<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 前月と今月
        $prev = Carbon::now()->subMonth();
        $now = Carbon::now();

        // user2
        $this->generateMonth($prev, 2, 'patternA');
        $this->generateMonth($now, 2, 'patternA');

        // user3
        $this->generateMonth($prev, 3, 'patternB');
        $this->generateMonth($now, 3, 'patternB');
    }

    private function generateMonth(Carbon $carbon, int $userId, string $pattern)
    {
        $start = $carbon->copy()->startOfMonth();
        $today = Carbon::today();

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
            
            Attendance::create([
                'user_id' => $userId,
                'date' => $date->format('Y-m-d'),
                'work_start' => $workStart,
                'work_end' => $workEnd,
                'status' => Attendance::STATUS_FINISHED,
            ]);
        }
    }
}

