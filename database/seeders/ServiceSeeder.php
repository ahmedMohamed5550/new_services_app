<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Service::updateOrCreate(
            ['name' => 'Service A'],
            [
                'desc' => 'Description for Service A',
                'section_id' => 1,
            ]
        );

        Service::updateOrCreate(
            ['name' => 'Service B'],
            [
                'desc' => 'Description for Service B',
                'section_id' => 2,
            ]
        );

        Service::updateOrCreate(
            ['name' => 'Service C'],
            [
                'desc' => 'Description for Service C',
                'section_id' => 3,
            ]
        );
    }
}
