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

    public function test_every_landing_section_is_rendered(): void
    {
        $this->seed(PlanSeeder::class);

        $html = $this->get('/')->assertOk()->getContent();

        foreach (['how-it-works', 'use-cases', 'features', 'styles', 'demo', 'analytics', 'pricing', 'faq', 'contact'] as $section) {
            $this->assertStringContainsString('id="'.$section.'"', $html, "Missing #{$section}");
        }
    }

    public function test_the_analytics_preview_is_labelled_as_sample_data(): void
    {
        $this->seed(PlanSeeder::class);

        $this->get('/')->assertSee([__('Example data'), __('Illustration with sample data, not real results.')]);
    }

    public function test_the_comparison_table_follows_plan_capabilities(): void
    {
        $this->seed(PlanSeeder::class);
        Plan::query()->whereNot('slug', 'free')->update(['is_active' => false]);

        // Only the free plan is left: EPS, analytics and priority support are all "not included".
        $this->get('/')
            ->assertSee(__('Compare plans'))
            ->assertSeeInOrder([__('EPS for professional print'), __('Not included'), __('Scan analytics'), __('Not included'), __('Priority support'), __('Not included')]);
    }

    public function test_it_shows_no_invented_social_proof(): void
    {
        $this->seed(PlanSeeder::class);

        $this->get('/')->assertDontSee(['testimonial', 'Trusted by', 'customers', 'happy users'], false);
    }

    public function test_the_hero_illustration_is_decorative_and_mirrorable(): void
    {
        $this->seed(PlanSeeder::class);

        $html = $this->get('/')->getContent();
        $scene = substr($html, strpos($html, 'data-hero-scene'), strpos($html, '</svg>', strpos($html, 'data-hero-scene')) - strpos($html, 'data-hero-scene'));

        $this->assertStringContainsString('rtl:-scale-x-100', $scene);
        $this->assertStringContainsString('aria-hidden="true"', $scene);
        $this->assertStringNotContainsString('<text', $scene, 'Text inside the drawing would read backwards when it is mirrored');
        $this->assertStringContainsString('data-part="phone"', $scene);
    }
}
