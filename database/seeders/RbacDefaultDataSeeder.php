<?php

namespace Database\Seeders;

use App\Models\Operation;
use App\Models\Permission;
use App\Models\RbacObject;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RbacDefaultDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Objects (কোন জিনিসে permission) ─────────────────
        $objects = [
            ['name' => 'Employee',     'slug' => 'employee',     'description' => 'Employee management'],
            ['name' => 'Salary',       'slug' => 'salary',       'description' => 'Salary information'],
            ['name' => 'Report',       'slug' => 'report',       'description' => 'Report generation'],
            ['name' => 'Security Log', 'slug' => 'security_log', 'description' => 'Security logs'],
            ['name' => 'Config',       'slug' => 'config',       'description' => 'System configuration'],
        ];

        foreach ($objects as $obj) {
            RbacObject::firstOrCreate(['slug' => $obj['slug']], $obj);
        }

        // ── Operations (কী করতে পারবে) ───────────────────────
        $operations = [
            ['name' => 'View',   'slug' => 'view',   'description' => 'View/read data'],
            ['name' => 'Create', 'slug' => 'create', 'description' => 'Create new records'],
            ['name' => 'Edit',   'slug' => 'edit',   'description' => 'Edit existing records'],
            ['name' => 'Delete', 'slug' => 'delete', 'description' => 'Delete records'],
            ['name' => 'Export', 'slug' => 'export', 'description' => 'Export/download data'],
        ];

        foreach ($operations as $op) {
            Operation::firstOrCreate(['slug' => $op['slug']], $op);
        }

        // ── Permissions (Object + Operation combination) ──────
        $allObjects    = RbacObject::all();
        $allOperations = Operation::all();

        foreach ($allObjects as $object) {
            foreach ($allOperations as $operation) {
                $slug = $object->slug . '.' . $operation->slug;
                Permission::firstOrCreate(
                    ['slug' => $slug],
                    [
                        'object_id'    => $object->id,
                        'operation_id' => $operation->id,
                        'slug'         => $slug,
                        'description'  => "{$operation->name} {$object->name}",
                    ]
                );
            }
        }

        // ── Default Roles ─────────────────────────────────────
        $roles = [
            [
                'name'        => 'HR Manager',
                'slug'        => 'hr_manager',
                'description' => 'Can manage employees and view reports',
                'permissions' => [
                    'employee.view', 'employee.create', 'employee.edit',
                    'salary.view',
                    'report.view', 'report.export',
                ],
            ],
            [
                'name'        => 'Accountant',
                'slug'        => 'accountant',
                'description' => 'Can view salary and export reports',
                'permissions' => [
                    'employee.view',
                    'salary.view', 'salary.export',
                    'report.view', 'report.export',
                ],
            ],
            [
                'name'        => 'Viewer',
                'slug'        => 'viewer',
                'description' => 'Read-only access to employee list',
                'permissions' => [
                    'employee.view',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            $permissions = $roleData['permissions'];
            unset($roleData['permissions']);

            $role = Role::firstOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );

            $permissionIds = Permission::whereIn('slug', $permissions)
                ->pluck('id');

            $role->permissions()->sync($permissionIds);
        }

        $this->command->info('RBAC default data seeded successfully!');
        $this->command->info('Objects: ' . RbacObject::count());
        $this->command->info('Operations: ' . Operation::count());
        $this->command->info('Permissions: ' . Permission::count());
        $this->command->info('Roles: ' . Role::count());
    }
}