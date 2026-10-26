<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Basicdata\Models\Branch;
use Modules\Usermanagement\Models\Permission;
use Modules\Usermanagement\Models\PermissionGroup;
use Modules\Usermanagement\Models\Position;
use Modules\Usermanagement\Models\Role;
use Modules\Usermanagement\Models\User;

class IgniteUserAndRoleSeeder extends Seeder
{
    /**
     * Run the database seeds for Ignite Editorial roles, permissions, and users.
     */
    public function run(): void
    {
        Log::info('Seeding Ignite roles, permissions, and users.');

        DB::beginTransaction();

        try {
            // 1. Seed Positions (Jabatan)
            $positionsData = [
                ['code' => 'ADM', 'name' => 'Administrator Sistem', 'level' => 1],
                ['code' => 'JM', 'name' => 'Journal Manager', 'level' => 2],
                ['code' => 'EIC', 'name' => 'Editor in Chief', 'level' => 2],
                ['code' => 'SE', 'name' => 'Section Editor', 'level' => 3],
                ['code' => 'REV', 'name' => 'Mitra Bestari / Peer Reviewer', 'level' => 4],
                ['code' => 'AUT', 'name' => 'Penulis / Author', 'level' => 5],
                ['code' => 'CE', 'name' => 'Language Editor / Copyeditor', 'level' => 4],
                ['code' => 'PR', 'name' => 'Layout Editor & Proofreader', 'level' => 4],
                ['code' => 'PROD', 'name' => 'Production Admin', 'level' => 3],
                ['code' => 'RDR', 'name' => 'Pembaca / Reader', 'level' => 6],
            ];

            $positions = [];
            foreach ($positionsData as $pos) {
                $positions[$pos['code']] = Position::query()->updateOrCreate(
                    ['code' => $pos['code']],
                    ['name' => $pos['name'], 'level' => $pos['level']]
                );
            }

            // 2. Seed Editorial Permissions
            $editorialGroup = PermissionGroup::query()->updateOrCreate(
                ['slug' => 'editorial'],
                ['name' => 'editorial']
            );

            $editorialPermissions = [
                'journals.read',
                'journals.create',
                'journals.update',
                'journals.delete',
                'journals.export',
                'journals.report',
                'issues.read',
                'issues.create',
                'issues.update',
                'issues.delete',
                'issues.export',
                'issues.report',
                'submissions.create',
                'submissions.read',
                'submissions.update',
                'submissions.delete',
                'submissions.review',
                'submissions.decide',
                'submissions.publish',
                'submissions.export',
                'editorial-boards.read',
                'editorial-boards.create',
                'editorial-boards.update',
                'editorial-boards.delete',
            ];

            foreach ($editorialPermissions as $permName) {
                Permission::query()->updateOrCreate(
                    ['name' => $permName, 'guard_name' => 'web'],
                    [
                        'module' => Str::before($permName, '.'),
                        'permission_group_id' => $editorialGroup->id,
                    ]
                );
            }

            // 3. Define Roles & Their Specific Permissions
            $rolesDefinition = [
                'administrator' => [
                    'position' => $positions['ADM']->id,
                    'permissions' => Permission::pluck('name')->toArray(), // All permissions
                ],
                'journal-admin' => [
                    'position' => $positions['JM']->id,
                    'permissions' => [
                        'journals.read', 'journals.create', 'journals.update', 'journals.delete', 'journals.export', 'journals.report',
                        'issues.read', 'issues.create', 'issues.update', 'issues.delete', 'issues.export', 'issues.report',
                        'submissions.create', 'submissions.read', 'submissions.update', 'submissions.review', 'submissions.decide', 'submissions.publish', 'submissions.export',
                        'editorial-boards.read', 'editorial-boards.create', 'editorial-boards.update', 'editorial-boards.delete',
                        'basic-data.read', 'usermanagement.read', 'system-logs.read', 'audit-logs.read',
                    ],
                ],
                'journal-manager' => [
                    'position' => $positions['JM']->id,
                    'permissions' => [
                        'journals.read', 'journals.create', 'journals.update', 'journals.delete', 'journals.export', 'journals.report',
                        'issues.read', 'issues.create', 'issues.update', 'issues.delete', 'issues.export', 'issues.report',
                        'submissions.create', 'submissions.read', 'submissions.update', 'submissions.review', 'submissions.decide', 'submissions.publish', 'submissions.export',
                        'editorial-boards.read', 'editorial-boards.create', 'editorial-boards.update', 'editorial-boards.delete',
                        'basic-data.read', 'usermanagement.read',
                    ],
                ],
                'editor-in-chief' => [
                    'position' => $positions['EIC']->id,
                    'permissions' => [
                        'journals.read', 'journals.update', 'journals.export',
                        'issues.read', 'issues.create', 'issues.update', 'issues.export',
                        'submissions.create', 'submissions.read', 'submissions.update', 'submissions.review', 'submissions.decide', 'submissions.publish', 'submissions.export',
                        'editorial-boards.read',
                    ],
                ],
                'editor' => [
                    'position' => $positions['SE']->id,
                    'permissions' => [
                        'journals.read',
                        'issues.read', 'issues.create', 'issues.update',
                        'submissions.read', 'submissions.update', 'submissions.review', 'submissions.decide', 'submissions.publish',
                    ],
                ],
                'section-editor' => [
                    'position' => $positions['SE']->id,
                    'permissions' => [
                        'journals.read',
                        'issues.read', 'issues.create', 'issues.update',
                        'submissions.read', 'submissions.update', 'submissions.review', 'submissions.decide',
                    ],
                ],
                'reviewer' => [
                    'position' => $positions['REV']->id,
                    'permissions' => [
                        'submissions.read',
                        'submissions.review',
                    ],
                ],
                'author' => [
                    'position' => $positions['AUT']->id,
                    'permissions' => [
                        'submissions.create',
                        'submissions.read',
                        'submissions.update',
                    ],
                ],
                'copyeditor' => [
                    'position' => $positions['CE']->id,
                    'permissions' => [
                        'submissions.read',
                        'submissions.update',
                    ],
                ],
                'proofreader' => [
                    'position' => $positions['PR']->id,
                    'permissions' => [
                        'submissions.read',
                        'issues.read',
                    ],
                ],
                'production-admin' => [
                    'position' => $positions['PROD']->id,
                    'permissions' => [
                        'journals.read',
                        'issues.read', 'issues.create', 'issues.update',
                        'submissions.read', 'submissions.publish',
                    ],
                ],
                'reader' => [
                    'position' => $positions['RDR']->id,
                    'permissions' => [],
                ],
            ];

            foreach ($rolesDefinition as $roleName => $config) {
                $role = Role::query()->updateOrCreate(
                    ['name' => $roleName, 'guard_name' => 'web'],
                    ['position_id' => $config['position']]
                );

                if (!empty($config['permissions'])) {
                    $validPermissions = Permission::whereIn('name', $config['permissions'])->get();
                    $role->syncPermissions($validPermissions);
                }
            }

            // 4. Seed Ignite Users
            $branch = Branch::first();
            if (!$branch) {
                $branch = Branch::create([
                    'code' => 'HQ',
                    'name' => 'Kantor Pusat Redaksi Ignite',
                    'status' => 'active',
                ]);
            }
            $branchId = $branch->id;

            $usersData = [
                [
                    'name'     => 'Ignite Administrator',
                    'email'    => 'admin@ignite.test',
                    'nik'      => '1001',
                    'roles'    => ['administrator'],
                ],
                [
                    'name'     => 'Journal Manager',
                    'email'    => 'journal.manager@ignite.test',
                    'nik'      => '1002',
                    'roles'    => ['journal-manager', 'journal-admin'],
                ],
                [
                    'name'     => 'Editor in Chief',
                    'email'    => 'eic@ignite.test',
                    'nik'      => '1003',
                    'roles'    => ['editor-in-chief', 'editor'],
                ],
                [
                    'name'     => 'Section Editor',
                    'email'    => 'section.editor@ignite.test',
                    'nik'      => '1004',
                    'roles'    => ['section-editor', 'editor'],
                ],
                [
                    'name'     => 'Reviewer 1',
                    'email'    => 'reviewer1@ignite.test',
                    'nik'      => '1005',
                    'roles'    => ['reviewer'],
                ],
                [
                    'name'     => 'Reviewer 2',
                    'email'    => 'reviewer2@ignite.test',
                    'nik'      => '1006',
                    'roles'    => ['reviewer'],
                ],
                [
                    'name'     => 'Author 1',
                    'email'    => 'author1@ignite.test',
                    'nik'      => '1007',
                    'roles'    => ['author'],
                ],
                [
                    'name'     => 'Author 2',
                    'email'    => 'author2@ignite.test',
                    'nik'      => '1008',
                    'roles'    => ['author'],
                ],
                [
                    'name'     => 'Copyeditor',
                    'email'    => 'copyeditor@ignite.test',
                    'nik'      => '1009',
                    'roles'    => ['copyeditor'],
                ],
                [
                    'name'     => 'Proofreader',
                    'email'    => 'proofreader@ignite.test',
                    'nik'      => '1010',
                    'roles'    => ['proofreader'],
                ],
                [
                    'name'     => 'Production Admin',
                    'email'    => 'production@ignite.test',
                    'nik'      => '1011',
                    'roles'    => ['production-admin'],
                ],
                [
                    'name'     => 'Reader',
                    'email'    => 'reader@ignite.test',
                    'nik'      => '1012',
                    'roles'    => ['reader'],
                ],
            ];

            $credentialsTable = [];

            foreach ($usersData as $data) {
                // Generate secure random alphanumeric password
                $plainPassword = Str::password(10, letters: true, numbers: true, symbols: false);

                $user = User::query()->updateOrCreate(
                    ['email' => $data['email']],
                    [
                        'name'              => $data['name'],
                        'password'          => Hash::make($plainPassword),
                        'nik'               => $data['nik'],
                        'branch_id'         => $branchId,
                        'email_verified_at' => now(),
                    ]
                );

                $user->syncRoles($data['roles']);

                $credentialsTable[] = [
                    'name'     => $data['name'],
                    'email'    => $data['email'],
                    'role'     => implode(', ', $data['roles']),
                    'password' => $plainPassword,
                ];
            }

            if ($this->command) {
                $this->command->info('=== DAFTAR KREDENSIAL PENGGUNA IGNITE ===');
                $this->command->table(['Nama', 'Email', 'Role', 'Password'], $credentialsTable);
            }

            // Ensure existing administrator account also has full administrator role
            $existingAdmin = User::where('email', 'administrator@ag.co.id')->first();
            if ($existingAdmin) {
                $existingAdmin->syncRoles(['administrator']);
            }

            DB::commit();
            Log::info('Ignite roles, permissions, and users seeded successfully.');
        } catch (\Throwable $throwable) {
            DB::rollBack();
            Log::error('Failed to seed Ignite roles and users: ' . $throwable->getMessage());
            throw $throwable;
        }
    }
}
