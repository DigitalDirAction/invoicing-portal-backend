<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'asim.maqbool@digitaldiraction.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'), // password
            'company' => 'Digital DirAction',
            'industry' => 'IT',
            'country' => 'Pakistan',
            'phone_number' => '+19087642456',
            'remember_token' => Str::random(10),
        ])->assignRole(Role::ADMIN);
    }
}
