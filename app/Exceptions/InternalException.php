<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class InternalException extends Exception
{


    public function __construct(string $message = "系统错误",int $code = 500)
    {
        parent::__construct($message, $code);

    }
    public function render(Request $request)
    {
        return response()->json(['message' => $this->message], $this->code);
    }
}
