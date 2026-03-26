<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['admin', 'teknisi', 'verifikator'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }

        $users = [
            [
                'name' => 'Novialdi',
                'email' => 'novialdi29@gmail.com',
                'role' => 'admin',
            ],
            [
                'name' => 'User Teknisi',
                'email' => 'teknisi@ti-documentation.test',
                'role' => 'teknisi',
            ],
            [
                'name' => 'User Verifikator',
                'email' => 'verifikator@ti-documentation.test',
                'role' => 'verifikator',
            ],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['role'] === 'admin' ? '123456789' : 'password'),
                ],
            );

            $user->syncRoles([$data['role']]);
        }
    }
}
