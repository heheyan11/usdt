<?php


namespace App\Exceptions;


use Illuminate\Http\Request;

class VerifyException extends \Exception
{
    protected $message;

    public function __construct(string $message = "验证失败")
    {
        $this->message = $message;
        parent::__construct($message, 422);

    }

    public function render(Request $request)
    {

        $message = [
            'message' => '验证失败',
            'error' => [
                $this->message
            ],
        ];


        return response()->json($message, 422);
    }
}