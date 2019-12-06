<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class AddWallet implements ShouldQueue
{

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Registered $event)
    {

        $guzzle = new \GuzzleHttp\Client();
        $url = 'http://localhost/api/test';

        $response = $guzzle->get($url);
        $rs = json_decode($response->getBody()->getContents(),true)['data']['eth'];
        $rs['kid'] = $rs['id'];
        $event->user->wallet()->create($rs);

    }
}
