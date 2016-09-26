<?php

/**
 * User
 */
$factory->define(Videouri\Entities\User::class, function (Faker\Generator $faker) {
    return [
        'username' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

/**
 * Video
 */
$factory->define(Videouri\Entities\Video::class, function (Faker\Generator $faker) {
    $faker->addProvider(new Faker\Provider\Internet($faker));

    $providers = [
        \Videouri\Entities\Source::YOUTUBE,
        \Videouri\Entities\Source::DAILYMOTION,
        \Videouri\Entities\Source::VIMEO,
    ];

    $original_id = $custom_id = str_random(5);
    $provider = $providers[array_rand($providers)];

    $data = [
        'url' => $faker->url(),
        'title' => $faker->sentence(),
        'description' => str_random(100),
        'thumbnail' => $faker->imageUrl(),
        'tags' => '',
    ];

    return [
        'provider' => $provider,
        'original_id' => $original_id,
        'custom_id' => $custom_id,

        'author' => $faker->userName(),
        'duration' => rand(5, 130),
        'views' => rand(5, 1500),
        'ratings' => rand(5, 130),

        'data' => json_encode($data)
    ];
});

$factory->define(Videouri\Entities\Favorite::class, function (Faker\Generator $faker) {
    $user = factory(Videouri\Entities\User::class)->create();
    $video = factory(Videouri\Entities\Video::class)->create();

    return [
        'user_id' => $user->id,
        'video_id' => $video->id,
    ];
});

$factory->define(Videouri\Entities\View::class, function (Faker\Generator $faker) {
    $user = factory(Videouri\Entities\User::class)->create();
    $video = factory(Videouri\Entities\Video::class)->create();

    return [
        'user_id' => $user->id,
        'video_id' => $video->id,
    ];
});

$factory->define(Videouri\Entities\Later::class, function (Faker\Generator $faker) {
    $user = factory(Videouri\Entities\User::class)->create();
    $video = factory(Videouri\Entities\Video::class)->create();

    return [
        'user_id' => $user->id,
        'video_id' => $video->id,
    ];
});

$factory->define(Videouri\Entities\Search::class, function (Faker\Generator $faker) {
    $user = factory(Videouri\Entities\User::class)->create();

    return [
        'term' => $faker->words(1, true),
        'user_id' => $user->id,
    ];
});
