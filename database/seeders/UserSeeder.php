<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->create([
            'username' => 'test',
            'password' => \Illuminate\Support\Facades\Hash::make('test'),
            'name' => 'test',
            'token' => 'test',
        ]);

        User::query()->create([
            'username' => 'test2',
            'password' => \Illuminate\Support\Facades\Hash::make('test2'),
            'name' => 'test2',
            'token' => 'test2',
        ]);
    }
}
