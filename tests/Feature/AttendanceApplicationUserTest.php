<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\Application;
use App\Models\ApplicationBreak;
use Carbon\Carbon;


class AttendanceApplicationUserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_error_messages_are_displayed_when_work_start_is_after_workend()
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

        $response = $this->post(route('user.application.store', [
            'attendance_id' => $attendance->id,
            'corrected_work_start' => '19:00',
            'corrected_work_end' => '18:00',
            'reason' => '打刻修正のテストのため'
        ]));

        $response->assertStatus(302);

        $response->assertSessionHasErrors([
            'corrected_work_start' => '出勤時間もしくは退勤時間が不適切な値です'
        ]);

        $this->assertDatabaseMissing('applications',[
            'attendance_id' => $attendance->id,
            'corrected_work_start' => '2026-06-18 19:00:00',
        ]);

        Carbon::setTestNow();
    }

    public function test_error_messages_are_displayed_when_break_start_is_after_workend()
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

        $response = $this->post(route('user.application.store', [
            'attendance_id' => $attendance->id,
            'corrected_work_start' => $attendance->work_start->format('H:i'),
            'corrected_work_end' => $attendance->work_end->format('H:i'),
            'corrected_break_start' => ['19:00'],
            'corrected_break_end' => ['20:00'],
            'reason' => '打刻修正のテストのため'
        ]));

        $response->assertStatus(302);

        $response->assertSessionHasErrors([
            'corrected_break_start.0' => '休憩時間が不適切な値です'
        ]);

        $this->assertDatabaseMissing('applications',[
            'attendance_id' => $attendance->id,
        ]);

        Carbon::setTestNow();
    }

    public function test_error_messages_are_displayed_when_break_end_is_after_workend()
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

        $response = $this->post(route('user.application.store', [
            'attendance_id' => $attendance->id,
            'corrected_work_start' => $attendance->work_start->format('H:i'),
            'corrected_work_end' => $attendance->work_end->format('H:i'),
            'corrected_break_start' => ['17:00'],
            'corrected_break_end' => ['19:00'],
            'reason' => '打刻修正のテストのため'
        ]));

        $response->assertStatus(302);

        $response->assertSessionHasErrors([
            'corrected_break_end.0' => '休憩時間もしくは退勤時間が不適切な値です'
        ]);

        $this->assertDatabaseMissing('applications',[
            'attendance_id' => $attendance->id,
        ]);

        Carbon::setTestNow();
    }

    public function test_error_messages_are_displayed_when_reason_is_not_entered()
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

        $response = $this->post(route('user.application.store', [
            'attendance_id' => $attendance->id,
            'corrected_work_start' => $attendance->work_start->format('H:i'),
            'corrected_work_end' => $attendance->work_end->format('H:i'),
            'corrected_break_start' => ['12:30'],
            'corrected_break_end' => ['13:30'],
            'reason' => '',
        ]));

        $response->assertStatus(302);

        $response->assertSessionHasErrors([
            'reason' => '備考を記入してください'
        ]);

        $this->assertDatabaseMissing('applications',[
            'attendance_id' => $attendance->id,
        ]);

        Carbon::setTestNow();
    }

    public function test_amendment_application_is_vidible_on_both_admin_application_list_and_approve_pages()
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

        $this->actingAs($user);

        $response = $this->post(route('user.application.store', [
            'attendance_id' => $attendance->id,
            'corrected_work_start' => $attendance->work_start->format('H:i'),
            'corrected_work_end' => $attendance->work_end->format('H:i'),
            'corrected_break_start' => ['12:30'],
            'corrected_break_end' => ['13:30'],
            'reason' => '打刻修正のため',
        ]));

        $response->assertStatus(302);

        $this->assertDatabaseHas('applications',[
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'reason' => '打刻修正のため',
            'status' => Application::STATUS_PENDING,
        ]);

        // 管理者でログインしなおす
        $this->actingAs($admin,'admin');

        $response = $this->get(route('application.list',[
            'status' => Application::STATUS_PENDING,
        ]));

        $response->assertStatus(200);
        
        $response->assertSee($user->name);
        $response->assertSee('打刻修正のため');

        $application = Application::where('attendance_id', $attendance->id)->first();

        $response = $this->get(route('admin.application.approve.show',[
            'attendance_correct_request_id' => $application->id,
        ]));
        
        $response->assertStatus(200);

        $response->assertSee($user->name);
        $response->assertSee('2026年');
        $response->assertSee('6月18日');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('12:30');
        $response->assertSee('13:30');
        $response->assertSee('打刻修正のため');

        Carbon::setTestNow();
    }

    public function test_pending_applications_are_displayed_on_application_list_page()
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

        $response = $this->post(route('user.application.store', [
            'attendance_id' => $attendance->id,
            'corrected_work_start' => $attendance->work_start->format('H:i'),
            'corrected_work_end' => $attendance->work_end->format('H:i'),
            'corrected_break_start' => ['12:30'],
            'corrected_break_end' => ['13:30'],
            'reason' => '打刻修正のため',
        ]));

        $response->assertStatus(302);

        $response = $this->get(route('application.list',[
            'status' => Application::STATUS_PENDING,
        ]));

        $response->assertStatus(200);

        $response->assertSee('打刻修正のため');

        Carbon::setTestNow();
    }

    public function test_approved_applications_are_displayed_on_application_list_page()
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

        $response = $this->post(route('user.application.store', [
            'attendance_id' => $attendance->id,
            'corrected_work_start' => $attendance->work_start->format('H:i'),
            'corrected_work_end' => $attendance->work_end->format('H:i'),
            'corrected_break_start' => ['12:30'],
            'corrected_break_end' => ['13:30'],
            'reason' => '打刻修正のため',
        ]));

        $response->assertStatus(302);

        $response = $this->get(route('application.list',[
            'status' => Application::STATUS_APPROVED,
        ]));

        Application::where('attendance_id', $attendance->id)
            ->update([
                'status' => Application::STATUS_APPROVED,
            ]);

        $response->assertStatus(200);

        $response->assertSee('打刻修正のため');

        Carbon::setTestNow();
    }

    public function test_screen_moves_to_attendance_detail_page_when_detail_button_is_clicked()
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

        $response = $this->post(route('user.application.store', [
            'attendance_id' => $attendance->id,
            'corrected_work_start' => $attendance->work_start->format('H:i'),
            'corrected_work_end' => $attendance->work_end->format('H:i'),
            'corrected_break_start' => ['12:30'],
            'corrected_break_end' => ['13:30'],
            'reason' => '打刻修正のため',
        ]));

        $this->assertDatabaseHas('applications',[
            'attendance_id' => $attendance->id,
            'reason' => '打刻修正のため',
        ]);

        $response = $this->get(route('application.list',[
            'status' => Application::STATUS_PENDING,
        ]));
        $response->assertStatus(200);

        $response = $this->get(route('user.attendance.show',[
            'id' => $attendance->id,
        ]));

        $response->assertStatus(200);

        $response->assertSee($user->name);
        $response->assertSee('6月18日');

        Carbon::setTestNow();
    }
}
