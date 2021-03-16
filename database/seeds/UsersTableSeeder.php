<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => (string) Str::uuid(),
            'name' => 'admin', 
            'email' => 'admin@mail.ru',
            'password' => '12345poi',
        ]);
        
    }
}
