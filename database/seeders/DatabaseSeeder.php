<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Logs\Database\Seeders\LogsDatabaseSeeder;
use Modules\Usermanagement\Database\Seeders\UsermanagementDatabaseSeeder;
use Modules\Location\Database\Seeders\LocationDatabaseSeeder;
use Modules\Basicdata\Database\Seeders\BasicdataDatabaseSeeder;

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
            LogsDatabaseSeeder::class,
            LocationDatabaseSeeder::class,
        ]);
    }
}
