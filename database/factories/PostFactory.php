<?php

use Faker\Generator as Faker;

$factory->define(App\Post::class, function (Faker $faker) {
    return [
        'text' => $faker->realText(),
        'user_id' => factory(App\User::class)->create()->id,
        'header' => $faker->text(),
        'status_id' => 0,
    ];
});

$factory->state(App\Post::class, 'active', [
    'status_id' => 1,
]);
