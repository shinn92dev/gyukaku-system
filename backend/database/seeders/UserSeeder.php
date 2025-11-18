<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $EMPLOYEES = [
            'server' => 15,
            'host' => 5,
            'kitchen' => 10,
            'dishwasher' => 5,
        ];

        $adminRole = Role::where('code', 'mgr')->first();

        $rolesMap = [
            // 'supervisor' => Role::where('code', 'sup')->first(),
            'server' => Role::where('code', 'srv')->first(),
            'kitchen' => Role::where('code', 'ktc')->first(),
            'dishwasher' => Role::where('code', 'dsh')->first(),
            'host' => Role::where('code', 'hst')->first(),
        ];
        

        // admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@gyukaku.com'],
            [
                'username' => 'admin',
                'password' => 'password',
                'first_name' => 'Admin',
                'last_name' => 'Admin',
                'phone_number' => '090-0000-0001',
                'date_of_birth' => '1990-01-01',
                'hire_date' => now()->subYears(5),
                'is_admin' => true,
                'is_active' => true,
            ]
        );
        $admin->roles()->syncWithoutDetaching($adminRole->id);

        foreach ($EMPLOYEES as $role => $count) {
            $this->createEmployees($rolesMap[$role], $count);
        }
    }

    private function createEmployees(Role $role, int $count): void
    {
        $users = User::factory()->count($count)->create();
        $role->users()->attach($users->pluck('id'));
    }
}
