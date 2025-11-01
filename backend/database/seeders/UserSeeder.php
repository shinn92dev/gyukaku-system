<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $managerRole = Role::where('code', 'mgr')->first();
        // $supervisorRole = Role::where('code', 'sup')->first();
        $serverRole = Role::where('code', 'srv')->first();
        // $kitchenRole = Role::where('code', 'ktc')->first();
        // $dishwasherRole = Role::where('code', 'dsh')->first();
        $hostRole = Role::where('code', 'hst')->first();

        // admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@gyukaku.com'],
            [
                'username' => 'admin',
                'password' => 'password',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'phone_number' => '090-0000-0001',
                'date_of_birth' => '1990-01-01',
                'hire_date' => now()->subYears(5),
                'is_admin' => true,
                'is_active' => true,
            ]
        );
        $admin->roles()->syncWithoutDetaching([$managerRole->id]);

        // Server/Host (dual role)
        $server = User::firstOrCreate(
            ['email' => 'server@gyukaku.com'],
            [
                'username' => 'server1',
                'password' => 'password',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone_number' => '090-0000-0003',
                'date_of_birth' => '2000-08-20',
                'hire_date' => now()->subYear(),
                'is_admin' => false,
                'is_active' => true,
            ]
        );
        $server->roles()->syncWithoutDetaching([$serverRole->id, $hostRole->id]);
    }
}
