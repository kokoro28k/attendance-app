<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceEndTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_status_changes_to_finished_when_finish_button_is_pressed()
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
            'work_start' => $fixedDateTime->toDateTimeString(),
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertSee('退勤');

        $response = $this->post(route('user.attendance.end'));

        $this->assertDatabaseHas('attendances',[
            'user_id' => $user->id,
            'status' => Attendance::STATUS_FINISHED,
            'work_start' => $fixedDateTime->toDateTimeString(),
        ]);
    }

    public function test_work_end_is_displayed_on_attendance_list_page()
    {
        $fixedDateTime = Carbon::create(2026,6,19,9,0,0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user);
        $this->post(route('user.attendance.start'));

        Attendance::where('user_id', $user->id)
            ->where('date', 'like', '2026-06-19%')
            ->update([
                'date' => '2026-06-19',
            ]);
            
        $fixedWorkEnd = $fixedDateTime->copy()->addHour();
        Carbon::setTestNow($fixedWorkEnd);

        $this->post(route('user.attendance.end'));
        
        $response = $this->get(route('user.attendance.index', [
            'year_month' => '2026-06',
        ]));
 
        $response->assertStatus(200);
        $response->assertSee('06/19');
        $response->assertSee('10:00');
         
        Carbon::setTestNow();
    }
}
