<?php

namespace App\Tests\Movie\Notifier\Factory;

use App\Movie\Notifier\Factory\ChainNotificationFactory;
use App\Movie\Notifier\Factory\DiscordNotificationFactory;
use App\Movie\Notifier\Factory\NotificationFactoryInterface;
use App\Movie\Notifier\Factory\SlackNotificationFactory;
use App\Movie\Notifier\Factory\TraceableChainNotificationFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Notifier\Notification\Notification;

class TraceableChainNotificationFactoryTest extends TestCase
{
    private static ?NullLogger $logger = null;
    private static iterable $factories = [];
    private static ?NotificationFactoryInterface $factory = null;

    public static function setUpBeforeClass(): void
    {
        static::$logger = new class extends NullLogger {
            private iterable $logs = [];

            public function log($level, string|\Stringable $message, array $context = []): void
            {
                $this->logs[] = sprintf("Level : %s - Message: \"%s\".", $level, $message);
            }

            public function getLogs(): iterable
            {
                return $this->logs;
            }
        };

        static::$factories = [
            'slack' => new SlackNotificationFactory(),
            'discord' => new DiscordNotificationFactory(),
        ];
        $chain = new ChainNotificationFactory(static::$factories);
        static::$factory = new TraceableChainNotificationFactory($chain, static::$logger);
    }

    /**
     * @group unit
     */
    public function testTraceableFactoryLogsBeforeCreatingNotification(): void
    {
        $notification = static::$factory->createNotification('subject', 'slack');

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertCount(1, static::$logger->getLogs());
        $this->assertSame(
            "Level : info - Message: \"Creating notification \"subject\" for slack channel\".",
            static::$logger->getLogs()[0]
        );
    }
}
