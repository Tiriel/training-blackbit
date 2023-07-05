<?php

namespace App\Event;

use App\Entity\Movie;
use App\Entity\User;

class MovieUnderageEvent extends AbstractMovieEvent
{
    private User $user;

    public function __construct(Movie $movie, User $user)
    {
        parent::__construct($movie);
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): MovieUnderageEvent
    {
        $this->user = $user;
        return $this;
    }
}
