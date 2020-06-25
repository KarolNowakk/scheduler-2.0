<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Shift;
use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(Shift::class, function (Faker $faker) {
    return [
        'worker_id' => 1,
        'work_place_id' => 1,
        'day' => '2020-06-17',
        'shift_start' => '09:00',
        'shift_end' => '17:00',
        'created_by' => 1,
    ];
});
