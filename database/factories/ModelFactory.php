<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
 */

// $factory->define(App\Entities\User::class, function (Faker\Generator $faker) {
//     $providers = ['twitter', 'facebook'];

//     return [
//         'username'       => $faker->name,
//         'email'          => $faker->email,
//         'password'       => bcrypt(str_random(10)),
//         'remember_token' => str_random(10),
//     ];
// });

$factory->define(App\Entities\Video::class, function (Faker\Generator $faker) {
    $providers = [
        'Youtube',
        'Vimeo',
        'Dailymotion',
        // 'Metacafe',
    ];

    $original_id = $custom_id = str_random(5);
    $provider = $providers[array_rand($providers)];

    return [
        'provider'     => $provider,
        'original_id'  => $original_id,
        'custom_id'    => $custom_id,
        'original_url' => $faker->url(),
        'author'       => $faker->userName(),
        'title'        => str_random(10),
        'description'  => str_random(100),
        'thumbnail'    => $faker->imageUrl(),
        'views'        => rand(5, 1500),
        'duration'     => rand(5, 130),
        'categories'   => [],
        'tags'         => [],
    ];
});
