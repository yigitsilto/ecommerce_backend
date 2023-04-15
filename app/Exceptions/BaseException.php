<?php

namespace FleetCart\Exceptions;

use Exception;

class BaseException
{

    /**
     * @param mixed $message
     */
    public static function responseServerError($message): \Illuminate\Http\JsonResponse
    {
        return response()->json(['error' => $message], 500);
    }

}
