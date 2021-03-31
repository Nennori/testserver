<?php

use Illuminate\Database\Seeder;
//use Encore\Admin\Auth\Database\AdminTablesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */

    public function run() {
        $this->call(RolesTableSeeder::class);
    }
}
