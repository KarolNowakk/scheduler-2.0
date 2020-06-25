<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Worker;
use Faker\Generator as Faker;

$factory->define(Worker::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'short_name' => $faker->firstName(),
        'job_title' => $faker->jobTitle,
        'work_place_id' => 1,
        'salary' => 18,
    ];
});
