<?php

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::firstOrNew(['name' => 'owner']);
        if (!$role->exists) {
            $role->name = 'owner';
            $role->edit_board = true;
            $role->edit_task = true;
            $role->save();
        }
        $role = Role::firstOrNew(['name' => 'user']);
        if (!$role->exists) {
            $role->name = 'user';
            $role->edit_board = false;
            $role->edit_task = true;
            $role->save();
        }
    }
}
