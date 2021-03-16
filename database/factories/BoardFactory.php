<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Board;
use Faker\Generator as Faker;

$factory->define(Board::class, function (Faker $faker) {
    return [
        // 'id' => $faker->uuid,
        'name' => $faker->name,
        'user_id' => $faker->uuid
    ];
});
