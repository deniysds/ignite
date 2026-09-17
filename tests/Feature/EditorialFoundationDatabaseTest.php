<?php

namespace Tests\Feature;

use Database\Seeders\EditorialRolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Modules\Usermanagement\Models\Permission;
use Modules\Usermanagement\Models\Role;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EditorialFoundationDatabaseTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function editorial_core_tables_are_available_for_mvp_workflow(): void
    {
        $this->assertTrue(Schema::hasTable('journals'));
        $this->assertTrue(Schema::hasTable('issues'));
        $this->assertTrue(Schema::hasTable('submissions'));
        $this->assertTrue(Schema::hasTable('submission_authors'));
        $this->assertTrue(Schema::hasTable('submission_files'));
        $this->assertTrue(Schema::hasTable('submission_histories'));
    }

    #[Test]
    public function editorial_roles_and_permissions_can_be_seeded(): void
    {
        $this->assertTrue(
            class_exists(EditorialRolesAndPermissionsSeeder::class),
            'Editorial roles and permissions seeder must exist.'
        );

        $this->seed(EditorialRolesAndPermissionsSeeder::class);

        $expectedRoles = ['journal-admin', 'editor', 'reviewer', 'author'];
        $expectedPermissions = [
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

        foreach ($expectedRoles as $roleName) {
            $this->assertDatabaseHas((new Role())->getTable(), ['name' => $roleName]);
        }

        foreach ($expectedPermissions as $permissionName) {
            $this->assertDatabaseHas((new Permission())->getTable(), ['name' => $permissionName]);
        }
    }
}
