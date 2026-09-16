<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
        'name' => 'Admin',
        'email' => 'admin@vj.com',
        'password' => bcrypt('admin123'),
        'role' => 'admin'
        ]);

    }
}
