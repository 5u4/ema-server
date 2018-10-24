<?php

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $missingPermissions = array_diff(Permission::PERMISSIONS, Permission::all()->toArray());

        foreach ($missingPermissions as $missingPermission) {
            Permission::create([
                'name' => $missingPermission,
            ]);
        }
    }
}
