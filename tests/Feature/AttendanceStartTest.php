<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendanceStartTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_status_changes_to_working_when_start_button_is_pressed()
    {

        $fixedDateTime = Carbon::create(2026,6,19,9,0,0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        // 今日のレコード（勤務外）を作成
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $fixedDateTime->toDateString(),
            'status' => Attendance::STATUS_OFF,
        ]);

        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance');

        $response->assertSee('出勤');

        $response = $this->post(route('user.attendance.start'));

        $this->assertDatabaseHas('attendances',[
            'user_id' => $user->id,
            'status' => Attendance::STATUS_WORKING,
            'work_start' => $fixedDateTime->toDateTimeString(),
        ]);
    }

    public function test_start_button_is_not_displayed_when_status_is_finished()
    {
        $fixedDateTime = Carbon::create(2026,6,19,9,0,0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        // 今日のレコード（退勤済）を作成
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $fixedDateTime->toDateString(),
            'status' => Attendance::STATUS_FINISHED,
        ]);

        $this->actingAs($user);
        
        $response = $this->get('/attendance');

        $response->assertDontSee('button--work_start');
    }

    public function test_work_start_is_displayed_on_attendance_list_page()
    {
        $fixedDateTime = Carbon::create(2026,6,19,9,0,0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user);
        $response = $this->post(route('user.attendance.start'));

        Attendance::where('user_id', $user->id)
            ->where('date', 'like', '2026-06-19%')
            ->update([
                'date' => '2026-06-19',
            ]);
            
        Carbon::setTestNow($fixedDateTime);
        
        $response = $this->get(route('user.attendance.index', [
            'year_month' => '2026-06',
        ]));
 
        $response->assertStatus(200);

        $content = $response->getContent();
        $response->assertSee('06/19');
        $response->assertSee('09:00');
         
        Carbon::setTestNow();
    }
}
