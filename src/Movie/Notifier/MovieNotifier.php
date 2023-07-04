<?php

namespace App\Movie\Notifier;

use App\Movie\Notifier\Factory\ChainNotificationFactory;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class MovieNotifier
{
    public function __construct(
        private readonly NotifierInterface $notifier,
        private readonly ChainNotificationFactory $factory,
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

        $this->notifier->send($notification, new Recipient($user->getEmail()));
    }
}
