<?php

namespace Database\Seeders;

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

        $role = Role::create(['name' => 'Super Admin']);


        $entities = [
            'accounts',
            'allocated_loans',
            'allocated_loan_installments',
            'company',
            'funds',
            'loans',
            'settings',
            'transactions',
            'users'
        ];

        foreach ($entities as $value) {
            Permission::create(['name' => 'view '.$value]);
            Permission::create(['name' => 'create '.$value]);
            Permission::create(['name' => 'edit '.$value]);
            Permission::create(['name' => 'delete '.$value]);
        }

//        Permission::create(['name' => 'view_accounts']);
//        Permission::create(['name' => 'view_allocated loans']);
//        Permission::create(['name' => 'view_allocated loan installment']);
//        Permission::create(['name' => 'view_company']);
//        Permission::create(['name' => 'view_funds']);
//        Permission::create(['name' => 'view_loans']);
//        Permission::create(['name' => 'view_settings']);
//        Permission::create(['name' => 'view_transactions']);
//        Permission::create(['name' => 'view_users']);
//
//        $manageAccounts = Permission::create(['name' => 'manage_accounts']);
//        $manageAllocatedLoans = Permission::create(['name' => 'manage_allocated loans']);
//        $manageAllocatedLoanInstallment = Permission::create(['name' => 'manage_allocated loan installment']);
//        $manageCompany = Permission::create(['name' => 'manage_company']);
//        $manageFunds = Permission::create(['name' => 'manage_funds']);
//        $manageLoans = Permission::create(['name' => 'manage_loans']);
//        $manageSettings = Permission::create(['name' => 'manage_settings']);
//        $manageTransactions = Permission::create(['name' => 'manage_transactions']);
//        $manageUsers = Permission::create(['name' => 'manage_users']);

//        $role->givePermissionTo($manageAccounts);
//        $role->givePermissionTo($manageAllocatedLoans);
//        $role->givePermissionTo($manageAllocatedLoanInstallment);
//        $role->givePermissionTo($manageCompany);
//        $role->givePermissionTo($manageFunds);
//        $role->givePermissionTo($manageLoans);
//        $role->givePermissionTo($manageSettings);
//        $role->givePermissionTo($manageTransactions);
//        $role->givePermissionTo($manageUsers);

        $user = User::where('SSN', 'admin')->first();
        $user->assignRole([$role->name]);
    }
}
