<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Logs\Database\Seeders\LogsDatabaseSeeder;
use Modules\Usermanagement\Database\Seeders\UsermanagementDatabaseSeeder;
use Modules\Location\Database\Seeders\LocationDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsermanagementDatabaseSeeder::class,
            LogsDatabaseSeeder::class,
            LocationDatabaseSeeder::class,
        ]);
    }
}
