<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            [
                'username' => 'admin',
            ],
            [
                'name' => 'Administrator',
                'phone_number' => null,
                'password' => 'Admin12345',
                'role' => 'admin',
            ]
        );

        ActivityLog::create([
            'table_name' => 'users',
            'record_id' => $admin->id,
            'created_by' => null,
            'created_at' => now(),
        ]);
    }
}