<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceDetailAdminTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_selected_attendance_is_displayed_on_admin_attendance_detail_page()
    {
        $fixedDateTime = Carbon::create(2026, 6, 18, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);
        $admin = User::factory()->create([
            'role' => 'admin',
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

        $this->actingAs($admin,'admin');

        $response = $this->get(route('admin.attendance.show', [
            'id' => $attendance->id,
        ]));

        $response->assertStatus(200);

        $response->assertSee($user->name);
        $response->assertSee('2026年');
        $response->assertSee('6月18日');
        $response->assertSee('09:00');
        $response->assertSee('18:00');

        Carbon::setTestNow();
    }

    public function test_error_messages_are_displayed_when_work_start_is_after_workend_on_admin_attendance_detail_page()
    {
        $fixedDateTime = Carbon::create(2026, 6, 18, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);
        $admin = User::factory()->create([
            'role' => 'admin',
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

        $this->actingAs($admin,'admin');

        $response = $this->put(route('admin.attendance.update', [
            'id' => $attendance->id,
            'work_start' => '19:00',
            'work_end' => '18:00',
            'note' => '打刻修正のテストのため'
        ]));

        $response->assertStatus(302);

        $response->assertSessionHasErrors([
            'work_start' => '出勤時間もしくは退勤時間が不適切な値です'
        ]);

        $this->assertDatabaseMissing('attendances',[
            'id' => $attendance->id,
            'work_start' => '2026-06-18 19:00:00',
        ]);

        Carbon::setTestNow();
    }

    public function test_error_messages_are_displayed_when_break_start_is_after_workend_on_admin_attendance_detail_page()
    {
        $fixedDateTime = Carbon::create(2026, 6, 18, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);
        $admin = User::factory()->create([
            'role' => 'admin',
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

        $this->actingAs($admin,'admin');

        $response = $this->put(route('admin.attendance.update', [
            'id' => $attendance->id,
            'work_start' => '09:00',
            'work_end' => '18:00',
            'break_start' => ['19:00'],
            'break_end' => ['20:00'],
            'note' => '打刻修正のテストのため'
        ]));

        $response->assertStatus(302);

        $response->assertSessionHasErrors([
            'break_start.0' => '休憩時間が不適切な値です'
        ]);

        $this->assertDatabaseMissing('attendances',[
            'id' => $attendance->id,
            'note' => '打刻修正のテストのため'
        ]);

        Carbon::setTestNow();
    }

    public function test_error_messages_are_displayed_when_break_end_is_after_workend_on_admin_attendance_detail_page()
    {
        $fixedDateTime = Carbon::create(2026, 6, 18, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);
        $admin = User::factory()->create([
            'role' => 'admin',
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

        $this->actingAs($admin,'admin');

        $response = $this->put(route('admin.attendance.update', [
            'id' => $attendance->id,
            'work_start' => '09:00',
            'work_end' => '18:00',
            'break_start' => ['17:00'],
            'break_end' => ['19:00'],
            'note' => '打刻修正のテストのため'
        ]));

        $response->assertStatus(302);

        $response->assertSessionHasErrors([
            'break_end.0' => '休憩時間もしくは退勤時間が不適切な値です'
        ]);

        $this->assertDatabaseMissing('attendances',[
            'id' => $attendance->id,
            'note' => '打刻修正のテストのため'
        ]);

        Carbon::setTestNow();
    }

    public function test_error_messages_are_displayed_when_note_is_not_entered()
    {
        $fixedDateTime = Carbon::create(2026, 6, 18, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);
        $admin = User::factory()->create([
            'role' => 'admin',
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

        $this->actingAs($admin,'admin');

        $response = $this->put(route('admin.attendance.update', [
            'id' => $attendance->id,
            'work_start' => '09:00',
            'work_end' => '18:00',
            'break_start' => ['12:30'],
            'break_end' => ['13:30'],
            'note' => '',
        ]));

        $response->assertStatus(302);

        $response->assertSessionHasErrors([
            'note' => '備考を記入してください'
        ]);

        $this->assertDatabaseMissing('attendances',[
            'id' => $attendance->id,
            'note' => '',
        ]);

        Carbon::setTestNow();
    }
}
