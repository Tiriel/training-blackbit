<?php

namespace App\Movie\Notifier\Factory;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\Notifier\Notification\Notification;

class ChainNotificationFactory implements NotificationFactoryInterface
{
    /** @var NotificationFactoryInterface[] $factories */
    private iterable $factories;

    public function __construct(
        #[TaggedIterator(tag: 'app.notification_factory', defaultIndexMethod: 'getIndex')]
        iterable $factories
    ) {
        $this->factories = $factories instanceof \Traversable ? iterator_to_array($factories) : $factories;
    }

    public function createNotification(string $subject, ?string $channel = null): Notification
    {
        if (!$channel) {
            throw new \RuntimeException(sprintf("%s::%s needs a valid channel as its second argument.", __CLASS__, __METHOD__));
        }
        return $this->factories[$channel]->createNotification($subject);
    }
}
