<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceDetailUserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_login_user_name_is_displayed_on_attendance_detail_page()
    {
        $fixedDateTime = Carbon::create(2026, 6, 18, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-06-18',
            'work_start' => Carbon::create(2026, 6, 18, 9, 0, 0),
            'work_end' => Carbon::create(2026, 6, 18, 18, 0, 0),
        ]);

        Attendance::where('user_id', $user->id)
        ->where('date', 'like', '2026-06-18%')
        ->update(['date' => '2026-06-18']);

        $this->actingAs($user);

        $response = $this->get(route('user.attendance.show', [
            'id' => $attendance->id,
        ]));

        $response->assertStatus(200);

        $response->assertSee($user->name);

        Carbon::setTestNow();
    }

    public function test_selected_date_is_displayed_on_attendance_detail_page()
    {
        $fixedDateTime = Carbon::create(2026, 6, 18, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-06-18',
            'work_start' => Carbon::create(2026, 6, 18, 9, 0, 0),
            'work_end' => Carbon::create(2026, 6, 18, 18, 0, 0),
        ]);

        Attendance::where('user_id', $user->id)
        ->where('date', 'like', '2026-06-18%')
        ->update(['date' => '2026-06-18']);

        $this->actingAs($user);

        $response = $this->get(route('user.attendance.show', [
            'id' => $attendance->id,
        ]));

        $response->assertStatus(200);

        $response->assertSee('2026年');
        $response->assertSee('6月18日');

        Carbon::setTestNow();
    }

    public function test_work_start_and_end_are_displayed_on_attendance_detail_page()
    {
        $fixedDateTime = Carbon::create(2026, 6, 18, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-06-18',
            'work_start' => Carbon::create(2026, 6, 18, 9, 0, 0),
            'work_end' => Carbon::create(2026, 6, 18, 18, 0, 0),
        ]);

        Attendance::where('user_id', $user->id)
        ->where('date', 'like', '2026-06-18%')
        ->update(['date' => '2026-06-18']);

        $this->actingAs($user);

        $response = $this->get(route('user.attendance.show', [
            'id' => $attendance->id,
        ]));

        $response->assertStatus(200);

        $response->assertSee('09:00');
        $response->assertSee('18:00');

        Carbon::setTestNow();
    }

    public function test_breaks_are_displayed_on_attendance_detail_page()
    {
        $fixedDateTime = Carbon::create(2026, 6, 18, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-06-18',
            'work_start' => Carbon::create(2026, 6, 18, 9, 0, 0),
            'work_end' => Carbon::create(2026, 6, 18, 18, 0, 0),
        ]);
        $attendance->breakTimes()->create([
            'break_start' => Carbon::create(2026, 6, 18, 12, 0, 0),
            'break_end' => Carbon::create(2026, 6, 18, 13, 0, 0),
        ]);

        Attendance::where('user_id', $user->id)
        ->where('date', 'like', '2026-06-18%')
        ->update(['date' => '2026-06-18']);

        $this->actingAs($user);

        $response = $this->get(route('user.attendance.show', [
            'id' => $attendance->id,
        ]));

        $response->assertStatus(200);

        $response->assertSee('12:00');
        $response->assertSee('13:00');

        Carbon::setTestNow();
    }
}
