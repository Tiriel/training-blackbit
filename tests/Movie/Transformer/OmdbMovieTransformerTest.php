<?php

namespace App\Tests\Movie\Transformer;

use App\Entity\Movie;
use App\Movie\Transformer\OmdbMovieTransformer;
use PHPUnit\Framework\TestCase;

class OmdbMovieTransformerTest extends TestCase
{
    private static ?OmdbMovieTransformer $transformer = null;

    public static function setUpBeforeClass(): void
    {
        static::$transformer = new OmdbMovieTransformer();
    }

    /**
     * @group unit
     */
    public function testTransformReturnsMovieObject(): void
    {
        $data = [
            'Title' => 'Star Wars',
            'Poster' => '',
            'Year' => '',
            'Released' => '',
            'Country' => '',
            'Plot' => '',
            'Rated' => '',
            'imdbID' => '',
        ];
        $movie = static::$transformer->transform($data);

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertSame('Star Wars', $movie->getTitle());
    }

    /**
     * @group unit
     */
    public function testTransformUsesYearWhenReleasedNotAvailable(): void
    {
        $data = [
            'Title' => 'Star Wars',
            'Poster' => '',
            'Year' => '1977',
            'Released' => 'N/A',
            'Country' => '',
            'Plot' => '',
            'Rated' => '',
            'imdbID' => '',
        ];
        $movie = static::$transformer->transform($data);

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals(new \DateTimeImmutable('01-01-1977'), $movie->getReleasedAt());

    }

    /**
     * @group unit
     */
    public function testTransformThrowsOnNotArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid data.');

        static::$transformer->transform(0);
    }

    /**
     * @group unit
     */
    public function testTransformThrowsOnMissingKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid data.');

        static::$transformer->transform(['Title' => '']);
    }
}
