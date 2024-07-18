<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminPermissions = Arr::flatten(config('authorization.permissions'));

        $role = Role::updateOrCreate(
            [
                "name" => Role::ADMIN
            ],
            [
                // Data to update or create
            ]
        )->syncPermissions($adminPermissions);
    }
}
