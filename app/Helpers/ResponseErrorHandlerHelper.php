<?php

namespace App\Helpers;

use App\Exceptions\ServiceException;
use Exception;
use Illuminate\Http\Response;

class ResponseErrorHandlerHelper
{
    public static function handle(Exception $e)
    {
        if ($e instanceof ServiceException) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    
        return response()->json([], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
