<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\EditorialRolesAndPermissionsSeeder;
use Database\Seeders\IgniteUserAndRoleSeeder;
use Modules\Issues\Database\Seeders\IssuesDatabaseSeeder;
use Modules\Journals\Database\Seeders\JournalsDatabaseSeeder;
use Modules\Logs\Database\Seeders\LogsDatabaseSeeder;
use Modules\Usermanagement\Database\Seeders\UsermanagementDatabaseSeeder;
use Modules\Location\Database\Seeders\LocationDatabaseSeeder;
use Modules\Basicdata\Database\Seeders\BasicdataDatabaseSeeder;
use Modules\Website\Database\Seeders\WebsiteDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BasicdataDatabaseSeeder::class,
            UsermanagementDatabaseSeeder::class,
            EditorialRolesAndPermissionsSeeder::class,
            LogsDatabaseSeeder::class,
            LocationDatabaseSeeder::class,
            JournalsDatabaseSeeder::class,
            IssuesDatabaseSeeder::class,
            WebsiteDatabaseSeeder::class,
            IgniteUserAndRoleSeeder::class,
        ]);
    }
}
