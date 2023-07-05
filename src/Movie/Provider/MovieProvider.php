<?php

namespace App\Movie\Provider;

use App\Entity\Movie;
use App\Movie\Consumer\OmdbMovieConsumer;
use App\Movie\Enum\SearchTypeEnum;
use App\Movie\Transformer\OmdbMovieTransformer;
use App\Repository\MovieRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class MovieProvider
{
    private ?SymfonyStyle $io = null;

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
        $this->io?->text('Checking on OMDb API...');
        $data = $this->consumer->fetchMovie($type, $search);
        $this->io?->text('Movie found!');

        if ($movie = $this->repository->findOneBy(['title' => $data['Title']])) {
            $this->io?->note('Movie already in Database!');
            return $movie;
        }

        $movie = $this->transformer->transform($data);

        foreach ($this->provider->getGenresFromOmdbString($data['Genre']) as $genre) {
            $movie->addGenre($genre);
        }

        $this->io?->text('Saving movie in database.');
        $this->repository->save($movie, true);

        return $movie;
    }

    public function setIo(?SymfonyStyle $io): void
    {
        $this->io = $io;
    }
}
