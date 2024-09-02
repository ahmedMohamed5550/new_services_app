<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::updateOrCreate(
            [
                'city' => 'Lake Sarahmouth',
                'bitTitle' => 'Howard Ridge',
                'street' => '97986 Darlene Plaza',
                'specialMarque' => 'Davis PLC',
                'lat' => 43.979282,
                'long' => 48.233496,
                'user_id' => 1,
            ],
        );

        Location::updateOrCreate(
            [
                'city' => 'South Thomasmouth',
                'bitTitle' => 'Adam Islands',
                'street' => '1581 Butler Circle',
                'specialMarque' => 'Gibson, Burns and Hughes',
                'lat' => -72.558646,
                'long' => -86.585523,
                'user_id' => 1,
            ],
        );
    }
}
