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
                'city' => 'Assiut',
                'bitTitle' => 'Howard Ridge',
                'street' => '97986 Darlene Plaza',
                'specialMarque' => 'Davis PLC',
                'lat' => 27.180956,
                'long' => 31.183683,
                'user_id' => 1,
            ],
        );

        Location::updateOrCreate(
            [
                'city' => 'Assiut',
                'bitTitle' => 'Adam Islands',
                'street' => '1581 Butler Circle',
                'specialMarque' => 'Gibson, Burns and Hughes',
                'lat' => 27.185658,
                'long' => 31.189061,
                'user_id' => 2,
            ],
        );

        Location::updateOrCreate(
            [
                'city' => 'Assiut',
                'bitTitle' => 'Adam Islands',
                'street' => '1581 Butler Circle',
                'specialMarque' => 'Gibson, Burns and Hughes',
                'lat' => 27.181587,
                'long' => 31.175978,
                'user_id' => 3,
            ],
        );

        Location::updateOrCreate(
            [
                'city' => 'Assiut',
                'bitTitle' => 'Adam Islands',
                'street' => '1581 Butler Circle',
                'specialMarque' => 'Gibson, Burns and Hughes',
                'lat' => 27.179998,
                'long' => 31.190001,
                'user_id' => 4,
            ],
        );

        Location::updateOrCreate(
            [
                'city' => 'Assiut',
                'bitTitle' => 'Adam Islands',
                'street' => '1581 Butler Circle',
                'specialMarque' => 'Gibson, Burns and Hughes',
                'lat' => 27.186456,
                'long' => 31.200123,
                'user_id' => 5,
            ],
        );

    }
}
