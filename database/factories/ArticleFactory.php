<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */


use App\Modules\Payments\Models\Article;
use Faker\Generator as Faker;

$factory->define(Article::class, function (Faker $faker) {
    return [
        'initials' => $faker->word,
        'display_name' => $faker->sentence,
        'description' => $faker->paragraph,
        'value' => $faker->randomFloat(2)
    ];
});
