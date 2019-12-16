<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class InternalException extends Exception
{


    protected $message;

    public function __construct(string $message = "系统错误")
    {
        $this->message = $message;
        parent::__construct($message, 500);

    }

    public function render(Request $request)
    {

        return response()->json(['message' => $this->message], 500);
    }
}
