<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\WorkPlace;
use Faker\Generator as Faker;

$factory->define(WorkPlace::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'address' => $faker->address,
        'logo_path' => $faker->url,
    ];
});
