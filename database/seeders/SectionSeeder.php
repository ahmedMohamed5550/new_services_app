<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Section::updateOrCreate(
            ['name' => 'Section A'],
            [
                'desc' => 'Description for Section A',
            ]
        );

        Section::updateOrCreate(
            ['name' => 'Section B'],
            [
                'desc' => 'Description for Section B',
            ]
        );

        Section::updateOrCreate(
            ['name' => 'Section C'],
            [
                'desc' => 'Description for Section C',
            ]
        );
    }
}
