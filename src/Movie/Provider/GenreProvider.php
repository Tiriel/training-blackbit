<?php

namespace App\Movie\Provider;

use App\Entity\Genre;
use App\Movie\Transformer\OmdgGenreTransformer;
use App\Repository\GenreRepository;

class GenreProvider
{
    public function __construct(
        private readonly GenreRepository $repository,
        private readonly OmdgGenreTransformer $transformer,
    ) {}

    public function getGenre(string $name): Genre
    {
        return $this->repository->findOneBy(['name' => $name])
            ?? $this->transformer->transform($name);
    }

    public function getGenresFromOmdbString(string $genres): \Generator
    {
        foreach (explode(', ', $genres) as $name) {
            yield $this->getGenre($name);
        }
    }
}
