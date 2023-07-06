<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }

    /**
     * @group functional
     */
    public function testNewMovieFormRedirectsOnSuccess(): void
    {
        $client = static::createClient();
        $adminUser = static::getContainer()
            ->get('App\Repository\UserRepository')
            ->findOneBy(['email' => 'admin@sensiolabs.com']);
        $client->loginUser($adminUser);

        $crawler = $client->request('GET', '/movie/new');
        $client->submitForm('Send', [
            'movie[title]' => 'Star Wars',
            'movie[poster]' => 'https://an.image.com/star%20wars.jpg',
            'movie[country]' => 'United States',
            'movie[releasedAt]' => '1977-05-25',
            'movie[plot]' => 'Everybody knows this plot, it\'s Hamlet in space.',
            'movie[price]' => 10,
        ]);
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
    }
}
