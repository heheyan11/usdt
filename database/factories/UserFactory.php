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

$factory->define(App\Models\User::class, function (Faker $faker) {

    for($i=0;$i<100;$i++){
        $arr[] = '13'.mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9);
    }
    $phone = $faker->randomElement($arr);

    return [
        'name' => $faker->name,
        'phone' => $phone,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$QY9rcJj50arVpSbUDRl5pe0Cq.tW.7QfPWZPyitnGmNW7NPFq9taq', // secret
        'remember_token' => str_random(10),
    ];
});
