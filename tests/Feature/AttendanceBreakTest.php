<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceBreakTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_status_changes_to_break_when_break_start_button_is_pressed()
    {

        $fixedDateTime = Carbon::create(2026,6,19,9,0,0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $fixedDateTime->toDateString(),
            'status' => Attendance::STATUS_WORKING,
        ]);

        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance');

        $response->assertSee('休憩入');

        $response = $this->post(route('user.break.start'));

        $this->assertDatabaseHas('attendances',[
            'user_id' => $user->id,
            'status' => Attendance::STATUS_BREAK,
        ]);

        $this->assertDatabaseHas('break_times',[
            'attendance_id' => $attendance->id,
            'break_start' => $fixedDateTime->toDateTimeString(),
        ]);
    }

    public function test_break_start_button_is_displayed_after_break_end()
    {
        $fixedDateTime = Carbon::create(2026,6,19,9,0,0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $fixedDateTime->toDateString(),
            'status' => Attendance::STATUS_WORKING,
        ]);

        $this->actingAs($user);

        $this->post(route('user.break.start'));

        $this->assertDatabaseHas('attendances',[
            'id' => $attendance->id,
            'status' => Attendance::STATUS_BREAK,
        ]);

        $this->post(route('user.break.end'));

        $this->assertDatabaseHas('attendances',[
            'id' => $attendance->id,
            'status' => Attendance::STATUS_WORKING,
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('休憩入');
    }

    
    public function test_break_end_chages_status_back_to_working()
    {
        $fixedDateTime = Carbon::create(2026,6,19,9,0,0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $fixedDateTime->toDateString(),
            'status' => Attendance::STATUS_WORKING,
        ]);

        $this->actingAs($user);

        $this->post(route('user.break.start'));

        $this->assertDatabaseHas('attendances',[
            'id' => $attendance->id,
            'status' => Attendance::STATUS_BREAK,
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('休憩戻');

        $this->post(route('user.break.end'));

        $this->assertDatabaseHas('attendances',[
            'id' => $attendance->id,
            'status' => Attendance::STATUS_WORKING,
        ]);
    }

     public function test_break_end_button_can_be_perfomed_multiple_times_in_a_day()
    {
        $fixedDateTime = Carbon::create(2026,6,19,9,0,0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $fixedDateTime->toDateString(),
            'status' => Attendance::STATUS_WORKING,
        ]);

        $this->actingAs($user);

        $this->post(route('user.break.start'));
        $this->assertDatabaseHas('attendances',[
            'id' => $attendance->id,
            'status' => Attendance::STATUS_BREAK,
        ]);

        $this->post(route('user.break.end'));
        $this->assertDatabaseHas('attendances',[
            'id' => $attendance->id,
            'status' => Attendance::STATUS_WORKING,
        ]);

        $this->post(route('user.break.start'));
        $this->assertDatabaseHas('attendances',[
            'id' => $attendance->id,
            'status' => Attendance::STATUS_BREAK,
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('休憩戻');
    }

    public function test_break_times_are_displayed_on_attendance_list_page()
    {

        $fixedDateTime = Carbon::create(2026,6,19,9,0,0, 'Asia/Tokyo');
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user);

        $this->post(route('user.attendance.start'));

        $attendance = Attendance::where('user_id', $user->id)->first();

        $this->post(route('user.break.start'));

        // 一時間後に休憩終了
        $fixedBreakEnd = $fixedDateTime->copy()->addHour();
        Carbon::setTestNow($fixedBreakEnd);
        $this->post(route('user.break.end'));

        // 休憩終了の一分後に退勤ボタンを押す
        $fixedWorkEnd = $fixedBreakEnd->copy()->addMinute(); // 10:01:00
        Carbon::setTestNow($fixedWorkEnd);
        $this->post(route('user.attendance.end'));

        $attendance->refresh();

        $this->assertDatabaseHas('attendances',[
            'id' => $attendance->id,
            'work_end' => '2026-06-19 10:01:00',
        ]);

        Attendance::where('user_id', $user->id)
            ->where('date', 'like', '2026-06-19%')
            ->update([
                'date' => '2026-06-19',
            ]);

        Carbon::setTestNow($fixedWorkEnd);

        $response = $this->get(route('user.attendance.index', [
            'year_month' => '2026-06',
        ]));

        $response->assertStatus(200);
        $response->assertSee('06/19');
        $response->assertSee('01:00');
         
        Carbon::setTestNow();
    }
}
