<?php

namespace App\Tests\Movie\Consumer;

use App\Movie\Consumer\OmdbMovieConsumer;
use App\Movie\Enum\SearchTypeEnum;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OmdbMovieConsumerTest extends TestCase
{
    public const MOVIE = '{"Title":"Star Wars: Episode IV - A New Hope","Year":"1977","Rated":"PG","Released":"25 May 1977","Runtime":"121 min","Genre":"Action, Adventure, Fantasy","Director":"George Lucas","Writer":"George Lucas","Actors":"Mark Hamill, Harrison Ford, Carrie Fisher","Plot":"The Imperial Forces, under orders from cruel Darth Vader, hold Princess Leia hostage in their efforts to quell the rebellion against the Galactic Empire. Luke Skywalker and Han Solo, captain of the Millennium Falcon, work together with the companionable droid duo R2-D2 and C-3PO to rescue the beautiful princess, help the Rebel Alliance and restore freedom and justice to the Galaxy.","Language":"English","Country":"United States","Awards":"Won 6 Oscars. 64 wins & 30 nominations total","Poster":"https://m.media-amazon.com/images/M/MV5BOTA5NjhiOTAtZWM0ZC00MWNhLThiMzEtZDFkOTk2OTU1ZDJkXkEyXkFqcGdeQXVyMTA4NDI1NTQx._V1_SX300.jpg","Ratings":[{"Source":"Internet Movie Database","Value":"8.6/10"},{"Source":"Rotten Tomatoes","Value":"93%"},{"Source":"Metacritic","Value":"90/100"}],"Metascore":"90","imdbRating":"8.6","imdbVotes":"1,376,600","imdbID":"tt0076759","Type":"movie","DVD":"06 Dec 2005","BoxOffice":"$460,998,507","Production":"N/A","Website":"N/A","Response":"True"}';

    /**
     * @group unit
     */
    public function testConsumerReturnsArray(): void
    {
        $responses = [
            new MockResponse(self::MOVIE),
        ];
        $client = new MockHttpClient($responses);

        $consumer = new OmdbMovieConsumer($client);
        $data = $consumer->fetchMovie(SearchTypeEnum::TITLE, 'Star Wars');

        $this->assertIsArray($data);
    }

    /**
     * @group unit
     */
    public function testConsumerThrowsWhenMovieNotFound(): void
    {
        $responses = [
            new MockResponse('{"Response":"False","Error":"Movie not found!"}')
        ];
        $client = new MockHttpClient($responses);
        $consumer = new OmdbMovieConsumer($client);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Movie not found!');

        $consumer->fetchMovie(SearchTypeEnum::TITLE, 'ssdfsdf');
    }
}
