<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'name' => 'abaky',
            'email' => 'abaky@gmail.com',
            'password' => Hash::make('Am123456'),
            'phone' => '01005763000',
            'userType' => 'user',
            'image' => null
        ]);

        User::updateOrCreate([
            'name' => 'ragab',
            'email' => 'ragab@gmail.com',
            'password' => Hash::make('Am123456'),
            'phone' => '01003980876',
            'userType' => 'employee',
            'image' => null
        ]);

        User::updateOrCreate([
            'name' => 'ragab2',
            'email' => 'ragab2@gmail.com',
            'password' => Hash::make('Am123456'),
            'phone' => '01003980871',
            'userType' => 'employee',
            'image' => null
        ]);
        User::updateOrCreate([
            'name' => 'ragab3',
            'email' => 'ragab3@gmail.com',
            'password' => Hash::make('Am123456'),
            'phone' => '01003980872',
            'userType' => 'employee',
            'image' => null
        ]);
        User::updateOrCreate([
            'name' => 'ragab4',
            'email' => 'ragab4@gmail.com',
            'password' => Hash::make('Am123456'),
            'phone' => '01003980873',
            'userType' => 'employee',
            'image' => null
        ]);
        User::updateOrCreate([
            'name' => 'ragab5',
            'email' => 'ragab5@gmail.com',
            'password' => Hash::make('Am123456'),
            'phone' => '01003980874',
            'userType' => 'employee',
            'image' => null
        ]);
        User::updateOrCreate([
            'name' => 'ragab6',
            'email' => 'ragab6@gmail.com',
            'password' => Hash::make('Am123456'),
            'phone' => '01003980875',
            'userType' => 'employee',
            'image' => null
        ]);
        User::updateOrCreate([
            'name' => 'ragab7',
            'email' => 'ragab7@gmail.com',
            'password' => Hash::make('Am123456'),
            'phone' => '01003980877',
            'userType' => 'employee',
            'image' => null
        ]);
        User::updateOrCreate([
            'name' => 'ragab8',
            'email' => 'ragab8@gmail.com',
            'password' => Hash::make('Am123456'),
            'phone' => '01003980878',
            'userType' => 'employee',
            'image' => null
        ]);
        User::updateOrCreate([
            'name' => 'ragab9',
            'email' => 'ragab9@gmail.com',
            'password' => Hash::make('Am123456'),
            'phone' => '01003980879',
            'userType' => 'employee',
            'image' => null
        ]);
        User::updateOrCreate([
            'name' => 'ragab10',
            'email' => 'ragab10@gmail.com',
            'password' => Hash::make('Am123456'),
            'phone' => '01003980866',
            'userType' => 'employee',
            'image' => null
        ]);
    }
}
