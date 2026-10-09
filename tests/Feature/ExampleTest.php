<?php

namespace Tests\Feature;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_home_page_renders_and_lists_plans(): void
    {
        Plan::create([
            'plan_id' => 'prod_starter',
            'price_id' => 'price_starter',
            'title' => 'Starter',
            'price' => 9,
            'qr_count' => 10,
            'scan_count' => 1000,
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Starter');
    }
}
