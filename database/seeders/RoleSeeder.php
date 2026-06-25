<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('mst_roles')->insert([
            [
                'mst_roles_id' => 1,
                'role_name' => 'Super Admin',
                'is_active' => 1,
                'is_delete' => 0,
                'create_date' => now()
            ],
            [
                'mst_roles_id' => 2,
                'role_name' => 'Admin',
                'is_active' => 1,
                'is_delete' => 0,
                'create_date' => now()
            ]
        ]);
    }
}
