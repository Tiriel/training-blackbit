<?php

namespace App\Tests\Movie\Provider;

use App\Entity\Genre;
use App\Movie\Provider\GenreProvider;
use App\Movie\Transformer\OmdbGenreTransformer;
use App\Repository\GenreRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GenreProviderTest extends TestCase
{
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
