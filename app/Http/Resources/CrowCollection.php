<?php

namespace App\Http\Resources;

use App\Models\Crowdfunding;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CrowCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'data' => $this->collection,
        ];
    }
}
