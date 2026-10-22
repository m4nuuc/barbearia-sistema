<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Corte', 'duration_minutes' => 40, 'price' => 53.00],
            ['name' => 'Barba', 'duration_minutes' => 20, 'price' => 40.00],
            ['name' => 'Corte + Barba', 'duration_minutes' => 60, 'price' => 83.00],
        ] as $service) {
            Service::firstOrCreate(['name' => $service['name']], $service);
        }
    }
}
