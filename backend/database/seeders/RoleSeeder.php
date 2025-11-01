<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['code' => 'mgr', 'display_name' => 'manager'],
            ['code' => 'sup', 'display_name' => 'supervisor'],
            ['code' => 'srv', 'display_name' => 'server'],
            ['code' => 'ktc', 'display_name' => 'kitchen'],
            ['code' => 'dsh', 'display_name' => 'dishwasher'],
            ['code' => 'hst', 'display_name' => 'host'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['code' => $role['code']],
                $role
            );
        }
    }
}
