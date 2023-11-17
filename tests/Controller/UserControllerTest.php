<?php

namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private  $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetAll()
    {
        $this->client->request('GET', '/api/user/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }
}