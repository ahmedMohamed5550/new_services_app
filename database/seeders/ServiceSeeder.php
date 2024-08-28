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
    public function run(): void
    {
        Service::create([
            'name' => 'Cleaning',
            'desc' => 'Professional cleaning services for homes and offices.',
            'image' => null
        ]);

        Service::create([
            'name' => 'Plumbing',
            'desc' => 'Expert plumbing services for repairs and installations.',
            'image' => null
        ]);
    }
}
