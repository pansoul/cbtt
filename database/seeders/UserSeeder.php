<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->truncate();
        DB::table('users')->insert([
            [
                'name' => 'Alex',
                'balance' => 1000,
                'email' => 'alex@gmail.com',
                'password' => Str::random(10),
            ],
            [
                'name' => 'Maria',
                'balance' => 500,
                'email' => 'maria@gmail.com',
                'password' => Str::random(10),
            ],
            [
                'name' => 'Kris',
                'balance' => 0,
                'email' => 'kris@gmail.com',
                'password' => Str::random(10),
            ],
            [
                'name' => 'John',
                'balance' => 100,
                'email' => 'john@gmail.com',
                'password' => Str::random(10),
            ],
            [
                'name' => 'Lucy',
                'balance' => 0,
                'email' => 'lucy@gmail.com',
                'password' => Str::random(10),
            ],
        ]);
    }
}
