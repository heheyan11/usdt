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

    for($i=0;$i<200;$i++){
        $arr[] = '13'.mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9);
    }
    $phone = $faker->randomElement($arr);

    return [
        'name' => $faker->name,
        'phone' => $phone,
        'headimgurl'=>'headimg.png',
       // 'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('secret'), // secret
       // 'remember_token' => str_random(10),
        'level'=>0,
        'path'=>'-'
    ];
});
