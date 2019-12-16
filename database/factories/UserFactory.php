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
        'headimgurl'=>'http://img14.360buyimg.com/mobilecms/s250x250_jfs/t1/36474/28/2429/517543/5cb9743aE168ee756/c70039f29f10f6b7.jpg',
       // 'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('secret'), // secret
       // 'remember_token' => str_random(10),
        'level'=>0,
        'path'=>'-'
    ];
});
