<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{
    /**
     * success response method
     *
     * @param $result
     * @param $message
     * @param $status
     * @return JsonResponse
     */

    public function sendSuccess($result, $message, $status): JsonResponse
    {
        $response = [
        'success' => true,
        'data' => $result,
        'message' => $message
        ];
        return response()->json($response, $status);
    }

    /**
     * error response method
     *
     * @param $error
     * @param $code
     * @return JsonResponse
     */

    public function sendError($error, $code): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];
        return response()->json($response, $code);
    }
}
