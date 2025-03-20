<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            PermissionSeeder::class,
        ]);

        $users = [
            [
                'name' => 'admin',
                'email' => 'admin@mail.com',
                'password' => '1234',
                'roles' => 'Admin'
            ],
        ];

        foreach ($users as $user) {
            $existingUser = User::firstOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => $user['password']
                ]
            );
            $existingUser->assignRole($user['roles']);
        }
    }
}
