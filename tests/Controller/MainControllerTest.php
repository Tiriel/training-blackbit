<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    /**
     * @group functional
     */
    public function testHomePageIsSuccessfulAndContainsCards(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $titleText = $crawler->filter('h1')->innerText();
        $cards = $crawler->filter('div.card');

        $this->assertResponseIsSuccessful();
        $this->assertSame('SensioTV+', $titleText);
        $this->assertCount(6, $cards);
    }

    /**
     * @group functional
     */
    public function testContactFormRedirectsOnSuccess()
    {
        $client = static::createClient();
        $adminUser = static::getContainer()
            ->get('App\Repository\UserRepository')
            ->findOneBy(['email' => 'admin@sensiolabs.com']);
        $client->loginUser($adminUser);

        $crawler = $client->request('GET', '/contact');
        $client->enableProfiler();
        $client->submitForm('Send', [
            'contact[name]' => 'John Doe',
            'contact[email]' => 'john@doe.com',
            'contact[subject]' => 'Contact attempt',
            'contact[message]' => 'Is this even working?',
        ]);
        $db = $client->getProfile()->getCollector('db');
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
    }
}
