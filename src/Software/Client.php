<?php

namespace App\Software;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
    /**
     * Undocumented variable.
     *
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

    public function get(string $url)
    {
        return $this->guzzle->get(
            $url
        );
    }

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
