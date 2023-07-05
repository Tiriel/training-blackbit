<?php

namespace App\EventSubscriber;

use App\Event\MovieCreatedEvent;
use App\Event\MovieUnderageEvent;
use App\Movie\Notifier\MovieNotifier;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MovieSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MovieNotifier $notifier)
    {
    }

    public function onMovieCreatedEvent(MovieCreatedEvent $event): void
    {
        $movie = $event->getMovie();
    }

    public function notifyAdmins(MovieUnderageEvent $event)
    {
        $user = $event->getUser();
        $movie = $event->getMovie();
        $msg = sprintf(
            "underage viewing attempt: User with identifier \"%s\" tried to watch movie id %s (\"%s\").",
            $user->getUserIdentifier(),
            $movie->getId(),
            $movie->getTitle()
        );

        $this->notifier->sendNotification($msg);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MovieCreatedEvent::class => 'onMovieCreatedEvent',
            MovieUnderageEvent::class => 'notifyAdmins'
        ];
    }
}
