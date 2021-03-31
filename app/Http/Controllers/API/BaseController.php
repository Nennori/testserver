<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use GuzzleHttp\Exception\BadResponseException;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
        $response = [
        'success' => true,
        'data' => $result,
        'message' => $message,
    ];
        return response()->json($response, 200);
    }
    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $code)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];
        return response()->json($response, $code);
    }
}
