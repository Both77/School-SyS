<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@school.com'],
            [
                'name' => 'Admin',
                'role' => 'admin',
                'password' => bcrypt('admin123'),
            ]
        );
    }
}
