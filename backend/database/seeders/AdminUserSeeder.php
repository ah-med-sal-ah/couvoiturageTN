<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the initial administrator account.
     *
     * Credentials are read from env so they never need to be hard-coded
     * into the frontend or committed with different values per environment.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => env('ADMIN_USERNAME', 'Ahmed')],
            [
                'first_name' => 'Ahmed',
                'last_name' => 'Admin',
                'cin' => '00000000',
                'age' => 30,
                'gender' => 'male',
                'password' => env('ADMIN_PASSWORD', 'Ahmed*123'),
                'is_admin' => true,
                'language' => 'en',
            ]
        );
    }
}
