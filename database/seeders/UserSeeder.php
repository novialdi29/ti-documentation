<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['admin', 'verifikator'];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        $users = [
            [
                'name' => 'Ibnu Daqiqil Id, S.Kom., M.T.I., Ph.D',
                'email' => 'ibnu.daqiqil@lecturer.unri.ac.id',
                'role' => 'verifikator',
                'password' => '1',
            ],
            [
                'name' => 'Aedi Kusmara, S.Pi',
                'email' => 'kuswara@staff.unri.ac.id',
                'role' => 'verifikator',
                'password' => '2',
            ],
            [
                'name' => 'Andi Saputra',
                'email' => 'andi.saputra@unri.ac.id',
                'role' => 'verifikator',
                'password' => '3',
            ],
            [
                'name' => 'Mohd. Teguh Wibowo, S.Kom',
                'email' => 'teguh@ict.unri.ac.id',
                'role' => 'admin',
                'password' => '4',
            ],
            [
                'name' => 'Jumiron, A.Md',
                'email' => 'jumiron@staff.unri.ac.id',
                'role' => 'admin',
                'password' => '5',
            ],
            [
                'name' => 'Teguh Permana Putra A.Md',
                'email' => 'teguh.permana@staff.unri.ac.id',
                'role' => 'admin',
                'password' => '6',
            ],
            [
                'name' => 'Herawati, S.Kom',
                'email' => 'herawati@unri.ac.id',
                'role' => 'verifikator',
                'password' => '7',
            ],
            [
                'name' => 'Novialdi T, S.T',
                'email' => 'novialdi.t@staff.unri.ac.id',
                'role' => 'admin',
                'password' => '8',
            ],
            [
                'name' => 'Andrio Arif Maulana, S.Kom',
                'email' => 'andrio.arif@staff.unri.ac.id',
                'role' => 'admin',
                'password' => '9',
            ],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['password']),
                    'email_verified_at' => now(),
                ],
            );

            $user->syncRoles([$data['role']]);
        }
    }
}
