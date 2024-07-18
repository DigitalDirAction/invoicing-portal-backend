<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Arr::flatten(config('authorization.permissions')) as $permission) {
            Permission::updateOrCreate([
                'name' => $permission,
                'description' => config('authorization.descriptions')[$permission],
            ]);
        }
    }
}
