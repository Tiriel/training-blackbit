<?php

namespace App\Tests\Movie\Transformer;

use App\Entity\Genre;
use App\Movie\Transformer\OmdbGenreTransformer;
use PHPUnit\Framework\TestCase;

class OmdbGenreTransformerTest extends TestCase
{
    private static ?OmdbGenreTransformer $transformer = null;

    public static function setUpBeforeClass(): void
    {
        static::$transformer = new OmdbGenreTransformer();
    }

    /**
     * @dataProvider provideGenreNames
     * @group unit
     */
    public function testTransformReturnsGenreObject(string $name): void
    {
        $genre = static::$transformer->transform($name);

        $this->assertInstanceOf(Genre::class, $genre);
        $this->assertSame($name, $genre->getName());
    }

    /**
     * @group unit
     */
    public function testTransformThrowsAnExceptionOnNonStringArgument(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Value must be a string, integer given.");

        static::$transformer->transform(123);
    }

    public function provideGenreNames(): \Generator
    {
        $data = [
            'Action' => ['Action'],
            'Adventure' => ['Adventure'],
            'Fantasy' => ['Fantasy'],
        ];

        foreach ($data as $index => $datum) {
            yield $index => $datum;
        }
    }
}
