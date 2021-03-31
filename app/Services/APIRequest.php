<?php


namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class APIRequest
{
    public function sendAPIRequest(string $base_url, string $url)
    {
        $client = new Client(['base_uri' => $base_url, 'timeout'  => 2.0]);
        try
        {
            $response = $client->request('GET', $url);
            return $response->getBody();
        }
        catch (BadResponseException $e)
        {
            return null;
        }
    }
}
