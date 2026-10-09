<?php

namespace Tests\Feature\Public;

use App\Models\Plan;
use App\Models\User;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_landing_page_lists_active_plans_only(): void
    {
        $this->seed(PlanSeeder::class);
        Plan::query()->where('slug', 'agency')->update(['is_active' => false]);

        $this->get('/')
            ->assertOk()
            ->assertSee([__('Print once. Change it any time.'), 'Starter', 'Business', '$29'])
            ->assertDontSee('Agency');
    }

    public function test_guests_are_invited_to_sign_up(): void
    {
        $this->seed(PlanSeeder::class);

        $this->get('/')->assertSee(route('register'))->assertSee(__('Start free'));
    }

    public function test_signed_in_users_see_their_current_plan(): void
    {
        $this->seed(PlanSeeder::class);

        $this->actingAs(User::factory()->create())->get('/')->assertSee(__('Current plan'));
    }
}
