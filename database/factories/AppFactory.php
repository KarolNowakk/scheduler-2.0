<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Shift;
use App\User;
use App\Worker;
use App\WorkPlace;
use App\Permission;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Shift::class, function (Faker $faker) {
    return [
        'worker_id' => random_int(1, 4),
        'work_place_id' => random_int(1, 2),
        'day' => $faker->date('Y-m-d'),
        'shift_start' => $faker->time('H:i'),
        'shift_end' => $faker->time('H:i'),
        'created_by' => random_int(1, 4),
    ];
});

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' =>  Str::random(10),
    ];
});

$factory->define(Worker::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'short_name' => $faker->firstName(),
        'job_title' => $faker->jobTitle,
        'work_place_id' => random_int(1, 2),
        'salary' => random_int(12, 25),
    ];
});

$factory->define(WorkPlace::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'address' => $faker->address,
        'logo_path' => $faker->url,
    ];
});

$factory->define(Permission::class, function (Faker $faker) {
    return [
        'user_id' => random_int(1, 5),
        'work_place_id' => random_int(1, 2),
        'type' => $faker->randomElement(['can_edit', 'can_create']),
        'created_by' => random_int(1, 5)
    ];
});
