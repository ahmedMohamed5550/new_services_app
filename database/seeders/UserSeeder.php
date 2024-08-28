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
        User::create([
            'name' => 'abaky',
            'email' => 'abaky@gmail.com',
            'password' => Hash::make('Am123456'),
            'phone' => '01005763000',
            'userType' => 'user',
            'image' => null
        ]);

        User::create([
            'name' => 'ragab',
            'email' => 'ragab@gmail.com',
            'password' => Hash::make('Am123456'),
            'phone' => '01003980876',
            'userType' => 'employee',
            'image' => null
        ]);
    }
}
