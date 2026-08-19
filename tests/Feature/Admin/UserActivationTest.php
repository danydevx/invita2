<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\UserController;
use App\Models\User;
use App\Services\ActivityService;
use App\Services\UserNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class UserActivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_verification_marks_the_email_as_verified(): void
    {
        $admin = User::factory()->create();
        $user = User::factory()->unverified()->create(['is_active' => true]);
        $activity = Mockery::mock(ActivityService::class);
        $activity->shouldReceive('log')->once()->with('user_verified_manually', Mockery::on(
            fn (array $options) => $options['user']->is($user) && $options['actor']->is($admin)
        ));
        $notifications = Mockery::mock(UserNotificationService::class);
        $notifications->shouldReceive('create')->once();
        $request = Request::create("/admin/users/{$user->id}/verify-email", 'PUT');
        $request->setUserResolver(fn () => $admin);
        app()->instance('request', $request);

        app(UserController::class)->verifyEmail($user, $activity, $notifications);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_manual_verification_does_not_change_the_active_status(): void
    {
        $admin = User::factory()->create();
        $user = User::factory()->unverified()->create(['is_active' => false]);
        $activity = Mockery::mock(ActivityService::class);
        $activity->shouldReceive('log')->once();
        $notifications = Mockery::mock(UserNotificationService::class);
        $notifications->shouldReceive('create')->once();
        $request = Request::create("/admin/users/{$user->id}/verify-email", 'PUT');
        $request->setUserResolver(fn () => $admin);
        app()->instance('request', $request);

        app(UserController::class)->verifyEmail($user, $activity, $notifications);

        $user->refresh();

        $this->assertFalse($user->is_active);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_activate_action_does_not_verify_the_email(): void
    {
        $admin = User::factory()->create();
        $user = User::factory()->unverified()->create(['is_active' => false]);
        $activity = Mockery::mock(ActivityService::class);
        $activity->shouldReceive('log')->once()->with('user_activated', Mockery::type('array'));
        $notifications = Mockery::mock(UserNotificationService::class);
        $notifications->shouldReceive('create')->once();
        $request = Request::create("/admin/users/{$user->id}/activate", 'PUT');
        $request->setUserResolver(fn () => $admin);
        app()->instance('request', $request);

        app(UserController::class)->activate($user, $activity, $notifications);

        $user->refresh();

        $this->assertTrue($user->is_active);
        $this->assertNull($user->email_verified_at);
    }
}
