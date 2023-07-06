<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    public function testBookIndexIsSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/book');

        $this->assertResponseIsSuccessful();
    }
}
