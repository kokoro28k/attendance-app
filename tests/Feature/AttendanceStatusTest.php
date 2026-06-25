<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendanceStatusTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_attendance_status_displays_off_when_status_is_off()
    {
        $fixedDate = Carbon::create(2026,6,19,10,00,00);
        Carbon::setTestNow($fixedDate);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        // 今日のレコード（勤務外）を作成
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $fixedDate->toDateString(),
            'status' => Attendance::STATUS_OFF,
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertSee('勤務外');
    }

    public function test_attendance_status_displays_working_when_status_is_working()
    {
        $fixedDate = Carbon::create(2026,6,19,10,00,00);
        Carbon::setTestNow($fixedDate);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        // 今日のレコード（勤務外）を作成
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $fixedDate->toDateString(),
            'status' => Attendance::STATUS_WORKING,
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertSee('出勤中');
    }

    public function test_attendance_status_displays_break_when_status_is_break()
    {
        $fixedDate = Carbon::create(2026,6,19,10,00,00);
        Carbon::setTestNow($fixedDate);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        // 今日のレコード（勤務外）を作成
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $fixedDate->toDateString(),
            'status' => Attendance::STATUS_BREAK,
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertSee('休憩中');
    }

    public function test_attendance_status_displays_finished_when_status_is_finished()
    {
        $fixedDate = Carbon::create(2026,6,19,10,00,00);
        Carbon::setTestNow($fixedDate);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        // 今日のレコード（勤務外）を作成
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $fixedDate->toDateString(),
            'status' => Attendance::STATUS_FINISHED,
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertSee('退勤済');
    }
}
