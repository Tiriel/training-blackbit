<?php

namespace App\Tests\Movie\Provider;

use App\Entity\Genre;
use App\Movie\Provider\GenreProvider;
use App\Movie\Transformer\OmdbGenreTransformer;
use App\Movie\Transformer\OmdbMovieTransformer;
use App\Repository\GenreRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class GenreProviderTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @group unit
     */
    public function testGetGenreReturnsObjectFromRepositoryWhenItExistsWithProphecy(): void
    {
        $actionGenre = (new Genre())->setName('Action');

        $transformer = $this->prophesize(OmdbGenreTransformer::class);
        $transformer->transform(Argument::any())->shouldNotBeCalled();

        $repository = $this->prophesize(GenreRepository::class);
        $repository->findOneBy(Argument::any())->shouldBeCalledOnce()->willReturn($actionGenre);

        $provider = new GenreProvider($repository->reveal(), $transformer->reveal());
        $genre = $provider->getGenre('Action');

        $this->assertInstanceOf(Genre::class, $genre);
        $this->assertSame($actionGenre, $genre);
    }

    /**
     * @group unit
     */
    public function testGetGenreReturnsObjectFromRepositoryWhenItExists(): void
    {
        $actionGenre = (new Genre())->setName('Action');
        $transformer = $this->getTransformerMock(null);
        $repository = $this->getRepositoryMock($actionGenre);

        $provider = new GenreProvider($repository, $transformer);
        $genre = $provider->getGenre('Action');

        $this->assertInstanceOf(Genre::class, $genre);
        $this->assertSame($actionGenre, $genre);
    }

    /**
     * @group unit
     */
    public function testGetGenreReturnsObjectFromTransformerWhenNotExists(): void
    {
        $actionGenre = (new Genre())->setName('Action');
        $transformer = $this->getTransformerMock($actionGenre);
        $repository = $this->getRepositoryMock(null);

        $provider = new GenreProvider($repository, $transformer);
        $genre = $provider->getGenre('Action');

        $this->assertInstanceOf(Genre::class, $genre);
        $this->assertSame($actionGenre, $genre);
    }

    /**
     * @group unit
     */
    public function testGetGenresFromOmdbStringReturnsGenerator(): void
    {
        $transformer = $this->getMockBuilder(OmdbGenreTransformer::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disallowMockingUnknownTypes()
            ->onlyMethods(['transform'])
            ->getMock()
            ;
        $transformer
            ->expects($this->exactly(3))
            ->method('transform')
            ->willReturn(new Genre())
        ;
        $repository = $this->createMock(GenreRepository::class);

        $provider = new GenreProvider($repository, $transformer);
        $result = $provider->getGenresFromOmdbString('Action, Adventure, Fantasy');

        $this->assertIsIterable($result);
        $this->assertCount(3, iterator_to_array($result));

    }

    private function getRepositoryMock(?Genre $return = null): GenreRepository|MockObject
    {
        $repository = $this->getMockBuilder(GenreRepository::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->onlyMethods(['findOneBy'])
            ->getMock()
        ;
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($return);

        return $repository;
    }

    private function getTransformerMock(?Genre $return = null): OmdbGenreTransformer|MockObject
    {
        $transformer = $this->getMockBuilder(OmdbGenreTransformer::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->onlyMethods(['transform'])
            ->getMock()
        ;
        $transformer
            ->expects($return ? $this->once() : $this->never())
            ->method('transform')
            ->willReturn($return)
        ;

        return $transformer;
    }
}
