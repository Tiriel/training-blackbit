<?php

namespace App\Tests\Movie\Notifier\Factory;

use App\Movie\Notifier\Factory\ChainNotificationFactory;
use App\Movie\Notifier\Factory\DiscordNotificationFactory;
use App\Movie\Notifier\Factory\SlackNotificationFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Notifier\Notification\Notification;

class ChainNotificationFactoryTest extends TestCase
{
    private static iterable $factories = [];
    private static ?ChainNotificationFactory $chainNotificationFactory = null;

    public static function setUpBeforeClass(): void
    {
        static::$factories = [
            'slack' => new SlackNotificationFactory(),
            'discord' => new DiscordNotificationFactory(),
        ];
        static::$chainNotificationFactory = new ChainNotificationFactory(static::$factories);
    }

    /**
     * @group unit
     */
    public function testChainFactoryConvertsIteratorsToArray(): void
    {
        $iterator = new \ArrayIterator(static::$factories);
        $chainFactory = new ChainNotificationFactory($iterator);

        $this->assertInstanceOf(Notification::class, $chainFactory->createNotification('subject', 'slack'));
    }

    /**
     * @group unit
     */
    public function testCreateNotificationReturnsNotificationObject(): void
    {
        $notification = static::$chainNotificationFactory->createNotification('subject', 'slack');

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertSame('subject', $notification->getSubject());
    }

    /**
     * @group unit
     */
    public function testCreateNotificationThrowsOnEmptyChannel(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(sprintf("%s::%s needs a valid channel as its second argument.", ChainNotificationFactory::class, 'createNotification'));

        static::$chainNotificationFactory->createNotification('subject');
    }

    /**
     * @group unit
     */
    public function testCreateNotificationThrowsOnInvalidChannel(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Channel sms is not defined. Available channels: 'slack', 'discord'");

        static::$chainNotificationFactory->createNotification('subject', 'sms');
    }
}
