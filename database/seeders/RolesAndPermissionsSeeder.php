<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',
            'menu.view', 'menu.create', 'menu.update', 'menu.delete',
            'sales.view', 'sales.create', 'sales.update', 'sales.delete',
            'expenses.view', 'expenses.create', 'expenses.update', 'expenses.delete',
            'customers.view', 'customers.create', 'customers.update', 'customers.delete',
            'credit.view', 'credit.create', 'credit.update', 'credit.delete',
            'investments.view', 'investments.create', 'investments.update', 'investments.delete',
            'reports.view',
            'users.view', 'users.create', 'users.update', 'users.delete',
            'roles.view', 'roles.create', 'roles.update', 'roles.delete',
            'activity.view',
            'categories.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $admin = Role::findOrCreate('Admin');
        $admin->syncPermissions($permissions);

        $manager = Role::findOrCreate('Manager');
        $manager->syncPermissions([
            'dashboard.view',
            'menu.view', 'menu.create', 'menu.update', 'menu.delete',
            'sales.view', 'sales.create', 'sales.update', 'sales.delete',
            'expenses.view', 'expenses.create', 'expenses.update', 'expenses.delete',
            'customers.view', 'customers.create', 'customers.update', 'customers.delete',
            'credit.view', 'credit.create', 'credit.update', 'credit.delete',
            'investments.view', 'investments.create', 'investments.update',
            'reports.view',
            'activity.view',
            'categories.manage',
        ]);

        $cashier = Role::findOrCreate('Cashier');
        $cashier->syncPermissions([
            'dashboard.view',
            'menu.view',
            'sales.view', 'sales.create',
            'expenses.view', 'expenses.create',
            'customers.view', 'customers.create', 'customers.update',
            'credit.view', 'credit.create',
            'investments.view',
        ]);
    }
}
