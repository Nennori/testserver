<?php
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
        DB::table('roles')->insert([
            'name' => 'administrator', 
            'edit_board' => true,
            'edit_task' => true
        ]);
        DB::table('roles')->insert([
            'name' => 'owner', 
            'edit_board' => true,
            'edit_task' => true
        ]);
        DB::table('roles')->insert([
            'name' => 'user', 
            'edit_board' => false,
            'edit_task' => true
        ]);
    }
}
