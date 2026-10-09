<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Plans everywhere; demo accounts and sample QR codes only outside production.
     * (No WithoutModelEvents: QrCode needs its "creating" event to get a short code.)
     */
    public function run(): void
    {
        $this->call(PlanSeeder::class);

        if (! app()->isProduction()) {
            $this->call(DemoSeeder::class);
        }
    }
}
