<?php

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $wechats = factory(\App\Models\Wechat::class,5)->create();
        foreach ($wechats as $wechat){
            factory(\App\Models\User::class,1)->create(['wechat_id'=>$wechat->id]);
        }
    }
}
