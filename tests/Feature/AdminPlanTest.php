<?php

namespace Tests\Feature;

use App\Filament\Resources\PlanResource\Pages\CreatePlan;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPlanTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Admin
    {
        return Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_admin_can_create_a_plan(): void
    {
        $this->actingAs($this->admin(), 'web');

        Livewire::test(CreatePlan::class)
            ->fillForm([
                'title' => 'Business',
                'plan_id' => 'prod_business',
                'price_id' => 'price_business',
                'price' => '29.00',
                'qr_count' => '50',
                'scan_count' => '10000',
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('plans', [
            'title' => 'Business',
            'plan_id' => 'prod_business',
            'price_id' => 'price_business',
            'qr_count' => 50,
            'scan_count' => 10000,
            'is_active' => true,
        ]);
    }

    public function test_admin_panel_plan_pages_render(): void
    {
        $this->actingAs($this->admin(), 'web');

        $this->get('/admin/plans')->assertOk();
        $this->get('/admin/plans/create')->assertOk();
    }

    public function test_customers_cannot_reach_the_admin_panel(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/admin/plans')->assertRedirect();
    }
}
