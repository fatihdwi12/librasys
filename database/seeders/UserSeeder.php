<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // forceCreate dipakai karena 'role' sengaja tidak ada di $fillable.
        // Password di-hash otomatis oleh cast 'hashed' pada model User.
        User::forceCreate([
            'name' => 'Admin LibraSys',
            'email' => 'admin@librasys.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        User::forceCreate([
            'name' => 'Ahmad Fauzi',
            'email' => 'member@librasys.test',
            'password' => 'password',
            'role' => 'member',
        ]);
    }
}