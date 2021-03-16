<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Task;

$factory->define(Task::class, function (Faker $faker) {
    return [
        'task_number' =>'1',
        'name' => $faker->title, 
        'description' => $faker->sentence, 
        'status_id' => $faker->uuid, 
        'board_id' =>$faker->uuid, 
        'expired_at' =>$faker->dateTime
    ];
});
