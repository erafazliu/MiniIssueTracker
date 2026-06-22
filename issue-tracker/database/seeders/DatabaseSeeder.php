<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;


    public function run(): void
    {
        $owner = User::query()->firstOrCreate([
            'email' => 'owner@example.com',
        ], [
            'name' => 'Era Fazliu',
            'password' => bcrypt('password'),
        ]);

        $member = User::query()->firstOrCreate([
            'email' => 'member@example.com',
        ], [
            'name' => 'Arber Fazliu',
            'password' => bcrypt('password'),
        ]);
    }
}
