<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class apiTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        echo strlen('00000000000000000005ac0b6971542b9c48c33874077337a15fc7f34f39a224');die;

        $guzzle = new \GuzzleHttp\Client();
        $url = 'http://192.168.10.10/api/test';

        $response = $guzzle->get($url);
        $rs = json_decode($response->getBody()->getContents(),true);

        $this->array(true);

    }

    public function testApi(){
        $response = $this->json('get', 'api/test');
        $response
            ->assertStatus(200)
            ->assertJson([
                'errcode'=>0,
            ]);

    }
}
