<?php

namespace App\Movie\Notifier;

use App\Movie\Notifier\Factory\ChainNotificationFactory;
use App\Movie\Notifier\Factory\NotificationFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class MovieNotifier
{
    public function __construct(
        private readonly NotifierInterface $notifier,
        private readonly NotificationFactoryInterface $factory,
    ) {}

    public function sendNotification(string $message): void
    {
        $user = new class {
            public function getEmail(): string {
                return 'test@test.com';
            }

            public function getPreferredChannel(): string {
                return 'slack';
            }
        };
        $notification = $this->factory->createNotification($message, $user->getPreferredChannel());

        //$this->notifier->send($notification, new Recipient($user->getEmail()));
        dump($notification);
    }
}
