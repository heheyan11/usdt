<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $card = $this->card_info;


        return [
            'id'=>$this->id,
            'username' => $this->name,
            'headimgurl' => $this->headimgurl,
            'phone' => substr_replace($this->phone, '****', 3, 4),
            'sex' => $this->sex,
            'card' => $card ? $card : [],
            'paypass' => $this->paypass ? 1 : 0,
            'share_code'=> $this->share_code
         ];
    }
}
