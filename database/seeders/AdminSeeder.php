<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gutta.id'],
            [
                'name'              => 'Gutta Admin',
                'email'             => 'admin@gutta.id',
                'whatsapp'          => '6281234567890',
                'password'          => Hash::make('Admin@Gutta2026!'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
