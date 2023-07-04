<?php

namespace App\Movie\Provider;

use App\Entity\Movie;
use App\Movie\Consumer\OmdbMovieConsumer;
use App\Movie\Enum\SearchTypeEnum;
use App\Movie\Transformer\OmdbMovieTransformer;
use App\Repository\MovieRepository;

class MovieProvider
{
    public function __construct(
        private readonly MovieRepository $repository,
        private readonly OmdbMovieConsumer $consumer,
        private readonly OmdbMovieTransformer $transformer,
        private readonly GenreProvider $provider,
    ) {}

    public function getMovieByTitle(string $title): Movie
    {
        return $this->getMovie(SearchTypeEnum::TITLE, $title);
    }

    public function getMovieById(string $id): Movie
    {
        return $this->getMovie(SearchTypeEnum::ID, $id);
    }

    public function getMovie(SearchTypeEnum $type, string $search): Movie
    {
        $data = $this->consumer->fetchMovie($type, $search);

        if ($movie = $this->repository->findOneBy(['title' => $data['Title']])) {
            return $movie;
        }

        $movie = $this->transformer->transform($data);

        foreach ($this->provider->getGenresFromOmdbString($data['Genre']) as $genre) {
            $movie->addGenre($genre);
        }

        $this->repository->save($movie, true);

        return $movie;
    }
}
