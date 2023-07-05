<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class UserSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly UserRepository $repository)
    {
    }

    public function storeLastConnection(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        if ($user instanceof User) {
            $user->setLastConnectedAt(new \DateTimeImmutable());
            $this->repository->save($user, true);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
             SecurityEvents::INTERACTIVE_LOGIN => 'storeLastConnection'
        ];
    }
}
