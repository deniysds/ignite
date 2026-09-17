<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Usermanagement\Models\Permission;
use Modules\Usermanagement\Models\PermissionGroup;
use Modules\Usermanagement\Models\Role;

class EditorialRolesAndPermissionsSeeder extends Seeder
{
    /**
     * Seeds the initial editorial roles and permissions for the MVP workflow.
     *
     * @return void
     */
    public function run(): void
    {
        Log::info('Seeding editorial roles and permissions.');

        DB::beginTransaction();

        try {
            $group = PermissionGroup::query()->updateOrCreate(
                ['slug' => 'editorial'],
                ['name' => 'editorial']
            );

            $permissions = [
                'journals.read',
                'journals.create',
                'journals.update',
                'issues.read',
                'issues.create',
                'issues.update',
                'submissions.create',
                'submissions.read',
                'submissions.update',
                'submissions.review',
                'submissions.decide',
                'submissions.publish',
            ];

            foreach ($permissions as $permissionName) {
                Permission::query()->updateOrCreate(
                    ['name' => $permissionName, 'guard_name' => 'web'],
                    [
                        'module' => Str::before($permissionName, '.'),
                        'permission_group_id' => $group->id,
                    ]
                );
            }

            $roles = [
                'journal-admin' => $permissions,
                'editor' => [
                    'journals.read',
                    'issues.read',
                    'issues.create',
                    'issues.update',
                    'submissions.read',
                    'submissions.update',
                    'submissions.review',
                    'submissions.decide',
                    'submissions.publish',
                ],
                'reviewer' => [
                    'submissions.read',
                    'submissions.review',
                ],
                'author' => [
                    'submissions.create',
                    'submissions.read',
                    'submissions.update',
                ],
            ];

            foreach ($roles as $roleName => $rolePermissions) {
                $role = Role::query()->firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => 'web',
                ]);

                $role->syncPermissions($rolePermissions);
            }

            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();

            Log::error('Failed to seed editorial roles and permissions.', [
                'message' => $throwable->getMessage(),
            ]);

            throw $throwable;
        }

        Log::info('Editorial roles and permissions seeded successfully.');
    }
}
