<?php

namespace App\Movie\Notifier\Factory;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\Notifier\Notification\Notification;

#[When(env: 'dev')]
#[AsDecorator(decorates: ChainNotificationFactory::class)]
class TraceableChainNotificationFactory implements NotificationFactoryInterface
{
    public function __construct(
        private readonly NotificationFactoryInterface $inner,
        private readonly LoggerInterface $logger,
    ) {}

    public function createNotification(string $subject, ?string $channel = null): Notification
    {
        $this->logger->info(sprintf("Creating notification \"%s\" for %s channel", $subject, $channel));

        return $this->inner->createNotification($subject, $channel);
    }
}
