<?php

namespace App\EventSubscriber;

use App\Event\MovieCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MovieSubscriber implements EventSubscriberInterface
{
    public function onMovieCreatedEvent(MovieCreatedEvent $event): void
    {
        $movie = $event->getMovie();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MovieCreatedEvent::class => 'onMovieCreatedEvent',
        ];
    }
}
