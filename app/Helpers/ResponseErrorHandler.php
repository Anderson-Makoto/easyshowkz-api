<?php

namespace App\Helpers;

use App\Exceptions\ServiceException;
use Exception;
use Illuminate\Http\Response;

function responseErrorHandler(Exception $e)
{
    if ($e instanceof ServiceException) {
        return response()->json($e->getMessage(), $e->getCode());
    }

    return response()->json([], Response::HTTP_INTERNAL_SERVER_ERROR);
}