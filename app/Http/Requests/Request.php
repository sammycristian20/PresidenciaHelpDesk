<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class Request extends FormRequest
{
    /**
     * Sends error response back by throwing HttpResponseException
     * @param $message |array
     * @param int $responseCode
     */
    protected function errorResponse($message, $responseCode = 400)
    {
        throw new HttpResponseException(errorResponse($message, $responseCode));
    }
}
