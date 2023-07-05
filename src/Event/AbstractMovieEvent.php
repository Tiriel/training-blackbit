<?php

namespace App\Event;

use App\Entity\Movie;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractMovieEvent extends Event
{
    public function __construct(private ?Movie $movie = null)
    {
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(Movie $movie): AbstractMovieEvent
    {
        $this->movie = $movie;
        return $this;
    }
}
