<?php

namespace App\Movie\Transformer;

use App\Entity\Movie;
use Symfony\Component\Form\DataTransformerInterface;

class OmdbMovieTransformer implements DataTransformerInterface
{
    public function __construct(
        private readonly OmdgGenreTransformer $genreTransformer
    ) {}

    public function transform(mixed $value)
    {
        if (!\is_array($value) || !array_key_exists('Title', $value)) {
            throw new \InvalidArgumentException("Invalid data.");
        }

        $date = $value['Released'] === 'N/A' ? '01-01-'.$value['Year'] : $value['Released'];

        $movie = (new Movie())
            ->setTitle($value['Title'])
            ->setPoster($value['Poster'])
            ->setPlot($value['Plot'])
            ->setCountry($value['Country'])
            ->setReleasedAt(new \DateTimeImmutable($date))
            ->setPrice(500)
            ;

        foreach (explode(', ', $value['Genre']) as $name) {
            $movie->addGenre($this->genreTransformer->transform($name));
        }

        return $movie;
    }

    public function reverseTransform(mixed $value)
    {
        throw new \RuntimeException('Not implemented.');
    }
}
