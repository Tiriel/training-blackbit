<?php

namespace App\Movie\Transformer;

use App\Entity\Movie;
use Symfony\Component\Form\DataTransformerInterface;

class OmdbMovieTransformer implements DataTransformerInterface
{
    private const KEYS = [
        'Title',
        'Poster',
        'Year',
        'Released',
        'Country',
        'Plot',
        'Rated',
        'imdbID',
    ];

    public function transform(mixed $value): mixed
    {
        if (!\is_array($value) || \count(array_diff(self::KEYS, array_keys($value))) > 0) {
            throw new \InvalidArgumentException("Invalid data.");
        }

        $date = $value['Released'] === 'N/A' ? '01-01-'.$value['Year'] : $value['Released'];

        $movie = (new Movie())
            ->setTitle($value['Title'])
            ->setPoster($value['Poster'])
            ->setPlot($value['Plot'])
            ->setCountry($value['Country'])
            ->setRated($value['Rated'])
            ->setImdbId($value['imdbID'])
            ->setReleasedAt(new \DateTimeImmutable($date))
            ->setPrice(500)
            ;

        return $movie;
    }

    public function reverseTransform(mixed $value): mixed
    {
        throw new \RuntimeException('Not implemented.');
    }
}
