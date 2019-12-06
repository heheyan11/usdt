<?php

namespace Tests\Unit;

use App\Models\Wechat;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WechatTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testInfoText()
    {

        $param = ['sdf'=>'dfd',['df32d'=>'df3d']];

        $this->sign($param);

        echo $this->html;
        $this->assertTrue(true);
    }
    public $html;

    public function sign($param){
        foreach ($param as $key=>$value){
            if(is_array($value)){
                return $this->sign($value);
            }
            $this->html .= ($key.'='.$value.'&');
        }
    }
}
