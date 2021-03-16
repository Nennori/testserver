<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;

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
        //(!empty($errorMessages)){
        //    $response['data'] = $errorMessages;
        //}
        return response()->json($response, $code);
    }
    public function sendAPIRequest(string $base_url, string $url)
    {
        $client = new Client(['base_uri' => $base_url, 'timeout'  => 2.0]);
            try {
                $response = $client->request('GET', $url);
                return $response->getBody();
            } catch (BadResponseException $e) {
                echo Psr7\Message::toString($e->getRequest());
                echo Psr7\Message::toString($e->getResponse());
                return null;
            }
    }
}
