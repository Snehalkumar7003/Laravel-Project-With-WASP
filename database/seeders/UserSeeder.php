<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('mst_users')->insert([
            'username' => 'Administrator',
            'email' => 'admin@admin.com',
            'mobile' => '9999999999',
            'password' => Hash::make('Admin@123'),
            'mst_roles_id' => 1,
            'is_first_login' => 1,
            'is_active' => 1,
            'is_delete' => 0,
            'create_date' => now()
        ]);
    }
}
