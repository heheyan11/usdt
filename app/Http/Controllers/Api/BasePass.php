<?php


namespace App\Http\Controllers\Api;


use App\Exceptions\InternalException;
use Laravel\Passport\Client;


class BasePass
{
    public $param;

    protected function blogin($username,$password){
        $this->param = [
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password
        ];
        return $this->passport();
    }

    protected function bFresh($token){
        $this->param=[
            'grant_type' => 'refresh_token',
            'refresh_token' => $token,
        ];
        return $this->passport();
    }

    protected function passport(){

        $password_client = Client::query()->where('password_client',1)->latest()->first();

        $param = array_merge($this->param,[
            'client_id' => $password_client->id,
            'client_secret' => $password_client->secret,
        ]);

       /* $guzzle = new \GuzzleHttp\Client();
        $url = resolve('Illuminate\Http\Request')->getSchemeAndHttpHost().'/oauth/token';
        try {
            $response = $guzzle->post($url, ['form_params'=>$param]);
        }catch (\GuzzleHttp\Exception\RequestException $e) {
            $resp = $e->getResponse();
            $code = $resp->getStatusCode();
            $content = json_decode((string)$resp->getBody(true));
        }*/

        $request = request();
        $request->request->add($param);
        $proxy = $request::create(
            'oauth/token',
            'POST'
        );
        $response = \Route::dispatch($proxy);

        return $response;
    }



    public function http($url, $method = 'GET', $postfields = null, $headers = array())
    {
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ci, CURLOPT_TIMEOUT, 30);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, true);
                if (!empty($postfields)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                }
                break;
        }
        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLINFO_HEADER_OUT, true);

        $response = curl_exec($ci);
        $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);

        curl_close($ci);
        return array($http_code, $response);
    }
}