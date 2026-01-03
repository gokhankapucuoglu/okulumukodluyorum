<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            'super_admin',
            'admin',
            'school_manager',
            'vice_manager',
            'teacher',
            'teacher_class',
            'teacher_library',
            'teacher_food',
            'student',
            'student_library',
            'student_food',
            'employee',
            'employee_food',
            'parent',
            'guest',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $managerRole = Role::firstOrCreate(['name' => 'school_manager', 'guard_name' => 'web']);
        $allPermissions = Permission::all();
        $forbiddenPermissions = [
            'delete_shield',
        ];
        $managerPermissions = $allPermissions->reject(function ($permission) use($forbiddenPermissions) {
            return in_array($permission->name, $forbiddenPermissions);
        });
        $managerRole->syncPermissions($managerPermissions);
        }
}
