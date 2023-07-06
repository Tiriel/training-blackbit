<?php

namespace App\Tests\Movie\Provider;

use App\Entity\Genre;
use App\Entity\Movie;
use App\Movie\Consumer\OmdbMovieConsumer;
use App\Movie\Enum\SearchTypeEnum;
use App\Movie\Provider\GenreProvider;
use App\Movie\Provider\MovieProvider;
use App\Movie\Transformer\OmdbMovieTransformer;
use App\Repository\MovieRepository;
use App\Tests\Movie\Consumer\OmdbMovieConsumerTest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MovieProviderTest extends TestCase
{
    /**
     * @group unit
     */
    public function testMovieProviderReturnsEntityWhenExists(): void
    {
        $entity = (new Movie())->setTitle('I\'m an entity');
        $repository = $this->getRepositoryMock($entity);

        $provider = new MovieProvider(
            $repository,
            $this->getConsumerMock(),
            new OmdbMovieTransformer(),
            $this->getGenreProvider(),
            $this->createMock(Security::class),
            $this->createMock(EventDispatcherInterface::class)
        );
        $result = $provider->getMovie(SearchTypeEnum::TITLE, 'entity');

        $this->assertSame($entity, $result);
    }

    /**
     * @group unit
     */
    public function testMovieProviderReturnsNewObjectWhenNotExists(): void
    {
        $provider = new MovieProvider(
            $this->getRepositoryMock(),
            $this->getConsumerMock(),
            new OmdbMovieTransformer(),
            $this->getGenreProvider(true),
            $this->createMock(Security::class),
            $this->createMock(EventDispatcherInterface::class)
        );
        $result = $provider->getMovie(SearchTypeEnum::TITLE, 'Star Wars');

        $this->assertInstanceOf(Movie::class, $result);
        $this->assertSame('Star Wars: Episode IV - A New Hope', $result->getTitle());
        $this->assertCount(3, $result->getGenres());
    }

    private function getRepositoryMock(?Movie $movie = null): MovieRepository|MockObject
    {
        $repository = $this->getMockBuilder(MovieRepository::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->onlyMethods(['findOneBy', 'save'])
            ->getMock()
            ;
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($movie)
        ;

        return $repository;
    }

    private function getConsumerMock(): OmdbMovieConsumer|MockObject
    {
        $consumer = $this->getMockBuilder(OmdbMovieConsumer::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disallowMockingUnknownTypes()
            ->onlyMethods(['fetchMovie'])
            ->getMock()
            ;
        $consumer
            ->expects($this->once())
            ->method('fetchMovie')
            ->willReturn(json_decode(OmdbMovieConsumerTest::MOVIE, true))
            ;

        return $consumer;
    }

    private function getGenreProvider(bool $return = false): GenreProvider|MockObject
    {
        $genreProvider = $this->getMockBuilder(GenreProvider::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disallowMockingUnknownTypes()
            ->onlyMethods(['getGenresFromOmdbString'])
            ->getMock()
            ;
        $genreProvider
            ->expects($return ? $this->once() : $this->never())
            ->method('getGenresFromOmdbString')
            ->will($this->returnCallback(function () {
                $genres = [
                    (new Genre())->setName('Action'),
                    (new Genre())->setName('Adventure'),
                    (new Genre())->setName('Fantasy'),
                ];

                foreach ($genres as $genre) {
                    yield $genre;
                }
            }));

        return $genreProvider;
    }
}
