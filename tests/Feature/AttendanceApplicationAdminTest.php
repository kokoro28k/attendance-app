<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Application;
use Carbon\Carbon;

class AttendanceApplicationAdminTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_admin_can_see_all_users_pending_application_on_pending_tab()
    {
         $fixedDateTime = Carbon::create(2026, 6, 18, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user1 = User::factory()->create([
            'role' => 'user',
            'name' => 'テスト太郎',
        ]);
        $user2 = User::factory()->create([
            'role' => 'user',
            'name' => 'テスト花子',
        ]);
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $attendance1 = Attendance::factory()->create(['user_id' => $user1->id]);
        $application1 = Application::factory()->create([
            'attendance_id' => $attendance1->id,
            'user_id' => $user1->id,
            'status' => Application::STATUS_PENDING, 
            'reason' => '打刻ミス修正',
        ]);

        $attendance2 = Attendance::factory()->create(['user_id' => $user2->id]);
        $application2 = Application::factory()->create([
            'attendance_id' => $attendance2->id,
            'user_id' => $user2->id,
            'status' => Application::STATUS_PENDING,
            'reason' => '電車遅延のため',
        ]);

        $this->actingAs($admin,'admin');

        $response = $this->get(route('application.list',[
            'status' => Application::STATUS_PENDING,
        ]));

        $response->assertStatus(200);

        $response->assertSee($user1->name);
        $response->assertSee('打刻ミス修正');
        $response->assertSee($user2->name);
        $response->assertSee('電車遅延のため');

        Carbon::setTestNow();
    }

    public function test_admin_can_see_all_users_approved_application_on_approved_tab()
    {
        $fixedDateTime = Carbon::create(2026, 6, 18, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user1 = User::factory()->create([
            'role' => 'user',
            'name' => 'テスト太郎',
        ]);
        $user2 = User::factory()->create([
            'role' => 'user',
            'name' => 'テスト花子',
        ]);
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $attendance1 = Attendance::factory()->create(['user_id' => $user1->id]);
        $application1 = Application::factory()->create([
            'attendance_id' => $attendance1->id,
            'user_id' => $user1->id,
            'status' => Application::STATUS_APPROVED, 
            'reason' => '打刻ミス修正',
        ]);

        $attendance2 = Attendance::factory()->create(['user_id' => $user2->id]);
        $application2 = Application::factory()->create([
            'attendance_id' => $attendance2->id,
            'user_id' => $user2->id,
            'status' => Application::STATUS_APPROVED,
            'reason' => '電車遅延のため',
        ]);

        $this->actingAs($admin,'admin');

        $response = $this->get(route('application.list',[
            'tab' => 'approved',
        ]));

        $response->assertStatus(200);

        $response->assertSee($user1->name);
        $response->assertSee('打刻ミス修正');
        $response->assertSee($user2->name);
        $response->assertSee('電車遅延のため');

        Carbon::setTestNow();
    }

    public function test_admin_can_see_pending_application_detail()
    {
        $fixedDateTime = Carbon::create(2026, 6, 18, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $attendance = Attendance::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => Application::STATUS_PENDING, 
            'reason' => '打刻ミス修正',
        ]);

        $this->actingAs($admin,'admin');

        $response = $this->get(route('admin.application.approve.show',[
            'attendance_correct_request_id' => $application->id,
        ]));

        $response->assertStatus(200);

        $response->assertSee($user->name);
        $response->assertSee('打刻ミス修正');

        Carbon::setTestNow();
    }

    public function test_pending_application_is_approved_and_updated_attendance()
    {
        $fixedDateTime = Carbon::create(2026, 6, 18, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $attendance = Attendance::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'corrected_work_start' => '10:00',
            'corrected_work_end' => '19:00',           
            'status' => Application::STATUS_PENDING, 
            'reason' => '打刻ミス修正',
        ]);

        $this->actingAs($admin,'admin');

        $response = $this->post(route('admin.application.approve',[
            'attendance_correct_request_id' => $application->id,
        ]));

        $response->assertRedirect();

        $this->assertDatabaseHas('applications',[
            'id' => $application->id,
            'status' => Application::STATUS_APPROVED,
        ]);

        $this->assertDatabaseHas('attendances',[
            'id' => $attendance->id,
            'work_start' => '2026-06-18 10:00:00', 
            'work_end' => '2026-06-18 19:00:00',
        ]);

        Carbon::setTestNow();
    }
}
