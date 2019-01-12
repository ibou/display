<?php

namespace App\Software;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
    /**
     * @var GuzzleClient
     */
    private $guzzle;

    /**
     * @param GuzzleClient $guzzle
     */
    public function __construct(GuzzleClient $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * Get Json.
     *
     * @param string $url
     */
    public function get(string $url)
    {
        return $this->guzzle->get(
            $url
        );
    }

    /**
     * Put Json data.
     *
     * @param string $url
     * @param array  $data
     */
    public function put($url, $data)
    {
        return $this->guzzle->put(
            $url,
            [
                'json' => $data,
                'headers' => [
                    'data-Type' => 'json',
                    'Content-Type' => 'application/json',
                ],
            ]
        );
    }
}
