<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\UserWallet::class, function (Faker $faker) {

    return [
        'amount'=> 0,
        'address'=> str_random(60),
        'privatekey'=> '0x4fa0c0d7b5723145cc8841db35e935839be4737438ea01b0f20387733b01624f',
        'kid'=> '567331529',
        "mnemonic"=> "check space fashion trouble bullet brass mistake advice dizzy action select section",
        "ostime"=> 1575627793
    ];
});
