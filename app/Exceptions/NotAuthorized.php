<?php

namespace App\Exceptions;
use Exception;

class NotAuthorized extends Exception
{
    public function render()
    {
        $responseJson['statusCode']=403;
        $responseJson['message']="You don't have the right permission";
        return response($responseJson,403);
    }
}
