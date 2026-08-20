<?php

namespace Tests\Feature\Member;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\ActivityService;
use App\Services\PlanLimits;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery;
use Modules\Listings\Models\Listing;
use Tests\TestCase;

class BusinessCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_plan_limits_uses_active_subscription(): void
    {
        $user = User::factory()->create();
        $plan = Plan::create([
            'name' => 'Business',
            'slug' => 'business',
            'is_active' => true,
            'limits' => ['max_businesses' => 3],
        ]);
        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'ends_at' => now()->addMonth(),
        ]);

        $this->assertSame(3, app(PlanLimits::class)->max($user, 'max_businesses'));
    }

    public function test_plan_limits_falls_back_to_free_plan(): void
    {
        $user = User::factory()->create();
        Plan::create([
            'name' => 'Free',
            'slug' => 'free',
            'is_active' => true,
            'limits' => ['max_businesses' => 1],
        ]);

        $this->assertSame(1, app(PlanLimits::class)->max($user, 'max_businesses'));
    }

    public function test_member_cannot_create_more_businesses_than_the_plan_allows(): void
    {
        $user = User::factory()->create();
        $planLimits = Mockery::mock(PlanLimits::class);
        $planLimits->shouldReceive('exceeded')->once()->andReturnTrue();
        $request = Request::create('/member/businesses', 'POST');
        $request->setUserResolver(fn () => $user);

        $response = app(\App\Http\Controllers\Member\BusinessController::class)->store(
            $request,
            $planLimits,
            Mockery::mock(ActivityService::class)
        );

        $this->assertSame(route('member.businesses.index'), $response->getTargetUrl());
        $this->assertSame(0, Listing::count());
    }
}
