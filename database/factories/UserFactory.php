<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

function getRandomPhone() {
    $prefix = [
        '134', '135', '136', '137', '138', '139', '150', '151', '152', '157', '130', '131', '132', '155', '186', '133', '153', '189',
    ];
    $prefix = $prefix[array_rand($prefix)];
    $middle = mt_rand(2000, 9000);
    $suffix = mt_rand(2000, 9000);
    return $prefix . $middle . $suffix;
}

$factory->define(App\Models\User::class, function (Faker $faker) {
    static $password;
    $time = date('Y-m-d H:i:s');
    return [
        'name' => $faker->name,
        'phone' => getRandomPhone(),
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'created_at' => $time,
        'updated_at' => $time
    ];
});
