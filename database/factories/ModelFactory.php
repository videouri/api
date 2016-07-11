<?php

/**
 * User
 */
$factory->define(App\Entities\User::class, function (Faker\Generator $faker) {
    return [
        'username'       => $faker->name,
        'email'          => $faker->email,
        'password'       => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

/**
 * Video
 */
$factory->define(App\Entities\Video::class, function (Faker\Generator $faker) {
    $faker->addProvider(new Faker\Provider\Internet($faker));

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
        'title'        => $faker->sentence(),
        'description'  => str_random(100),
        'thumbnail'    => $faker->imageUrl(),
        'views'        => rand(5, 1500),
        'duration'     => rand(5, 130),
        'categories'   => '',
        'tags'         => '',
    ];
});

$factory->define(App\Entities\Favorite::class, function (Faker\Generator $faker) {
    $user = factory(App\Entities\User::class)->create();
    $video = factory(App\Entities\Video::class)->create();

    return [
        'user_id' => $user->id,
        'video_id' => $video->id,
    ];
});

$factory->define(App\Entities\View::class, function (Faker\Generator $faker) {
    $user = factory(App\Entities\User::class)->create();
    $video = factory(App\Entities\Video::class)->create();

    return [
        'user_id' => $user->id,
        'video_id' => $video->id,
    ];
});

$factory->define(App\Entities\Later::class, function (Faker\Generator $faker) {
    $user = factory(App\Entities\User::class)->create();
    $video = factory(App\Entities\Video::class)->create();

    return [
        'user_id' => $user->id,
        'video_id' => $video->id,
    ];
});

$factory->define(App\Entities\Search::class, function (Faker\Generator $faker) {
    $user = factory(App\Entities\User::class)->create();

    return [
        'term' => $faker->words(1, true),
        'user_id' => $user->id,
    ];
});