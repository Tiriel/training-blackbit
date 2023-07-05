<?php

namespace App\Movie\Provider;

use App\Entity\Movie;
use App\Entity\User;
use App\Event\MovieCreatedEvent;
use App\Movie\Consumer\OmdbMovieConsumer;
use App\Movie\Enum\SearchTypeEnum;
use App\Movie\Transformer\OmdbMovieTransformer;
use App\Repository\MovieRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MovieProvider
{
    private ?SymfonyStyle $io = null;

    public function __construct(
        private readonly MovieRepository $repository,
        private readonly OmdbMovieConsumer $consumer,
        private readonly OmdbMovieTransformer $transformer,
        private readonly GenreProvider $provider,
        private readonly Security $security,
        private readonly EventDispatcherInterface $dispatcher,
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

        if (($user = $this->security->getUser()) instanceof User) {
            $movie->setCreatedBy($user);
        }

        $this->io?->text('Saving movie in database.');
        $this->repository->save($movie, true);
        $this->dispatcher->dispatch(new MovieCreatedEvent($movie));

        return $movie;
    }

    public function setIo(?SymfonyStyle $io): void
    {
        $this->io = $io;
    }
}
