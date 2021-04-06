<?php

use App\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DBAssistant::resetTable('roles');
        DBAssistant::resetTable('permissions');
        DBAssistant::resetTable('role_has_permissions');

        $role = Role::create(['name' => 'admin']);
        $permission = Permission::create(['name' => 'access to everything']);
        $role->givePermissionTo($permission);

        $user = User::where('SSN', 'admin')->first();
        $user->assignRole([$role->name]);
    }
}
