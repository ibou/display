<?php

namespace App\Tests\Software;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use App\Software\Client;
use GuzzleHttp\Client as GuzzleClient;

class ClientTest extends TestCase
{
    private $url;
    private $client;

    public function setUp()
    {
        $this->url = 'http://mockbin.com';
        $this->client = new Client(new GuzzleClient());
    }

    public function testGet()
    {
        $response = $this->client->get("{$this->url}/request?foo=bar");
        $data = json_decode($response->getBody(), true);

        $this->assertEquals('GET', $data['method']);
        $this->assertEquals('json', $data['headers']['data-type']);
        $this->assertEquals('application/json', $data['headers']['content-type']);
    }

    public function testPut()
    {
        $response = $this->client->put("{$this->url}/request?foo=bar", ['test' => 'value data']);
        $data = json_decode($response->getBody(), true);
        $this->assertEquals('PUT', $data['method']);
        $this->assertEquals('json', $data['headers']['data-type']);
        $this->assertEquals('application/json', $data['headers']['content-type']);
        $this->assertEquals(json_encode(['test' => 'value data']), $data['postData']['text']);
    }
}
