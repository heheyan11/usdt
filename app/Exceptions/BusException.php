<?php


namespace App\Exceptions;


use Illuminate\Http\Request;

class BusException extends \Exception
{
    protected $code;
    protected $message;
    public function __construct(string $message = "业务错误", int $code = 500)
    {


        $this->code = $code;
        $this->$message = $message;

        parent::__construct($message, $code);

    }

    public function render(Request $request)
    {
        $msg = ['message' => $this->message, 'code' => $this->code];
        return response()->json($msg, 200);
    }
}