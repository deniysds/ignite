<?php

namespace Tests\Feature;

use Database\Seeders\IgniteUserAndRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Usermanagement\Models\Permission;
use Modules\Usermanagement\Models\Position;
use Modules\Usermanagement\Models\Role;
use Modules\Usermanagement\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class IgniteUserAndRoleSeederTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function ignite_roles_permissions_and_users_can_be_seeded_correctly(): void
    {
        $this->seed(IgniteUserAndRoleSeeder::class);

        $expectedRoles = [
            'administrator',
            'journal-admin',
            'journal-manager',
            'editor-in-chief',
            'editor',
            'section-editor',
            'reviewer',
            'author',
            'copyeditor',
            'proofreader',
            'production-admin',
            'reader',
        ];

        foreach ($expectedRoles as $role) {
            $this->assertDatabaseHas((new Role())->getTable(), ['name' => $role]);
        }

        $expectedUsers = [
            'admin@ignite.test',
            'journal.manager@ignite.test',
            'eic@ignite.test',
            'section.editor@ignite.test',
            'reviewer1@ignite.test',
            'author1@ignite.test',
            'copyeditor@ignite.test',
            'production@ignite.test',
        ];

        foreach ($expectedUsers as $email) {
            $this->assertDatabaseHas((new User())->getTable(), ['email' => $email]);
        }

        $expectedPositions = ['ADM', 'JM', 'EIC', 'SE', 'REV', 'AUT', 'CE', 'PR', 'PROD', 'RDR'];
        foreach ($expectedPositions as $code) {
            $this->assertDatabaseHas((new Position())->getTable(), ['code' => $code]);
        }
    }
}
