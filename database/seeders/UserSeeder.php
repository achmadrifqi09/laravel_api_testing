<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Achmad Rifqi',
            'username' => 'achmadrifqi09',
            'password' => Hash::make('rahasia'),
            'token' => 'test'
        ]);
    }
}
