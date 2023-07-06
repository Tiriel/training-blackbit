<?php

namespace App\Tests\Movie\Notifier\Factory;

use App\Movie\Notifier\Factory\DiscordNotificationFactory;
use App\Movie\Notifier\Notification\DiscordNotification;
use PHPUnit\Framework\TestCase;

class DiscordNotificationFactoryTest extends TestCase
{
    private static ?DiscordNotificationFactory $factory = null;

    public static function setUpBeforeClass(): void
    {
        static::$factory = new DiscordNotificationFactory();
    }

    /**
     * @group unit
     */
    public function testFactoryReturnsDiscordNotificationObject(): void
    {
        $notification = static::$factory->createNotification('subject');

        $this->assertInstanceOf(DiscordNotification::class, $notification);
        $this->assertSame('subject', $notification->getSubject());
    }

    /**
     * @group unit
     */
    public function testFactoryGetIndexMethodReturnsDiscord()
    {
        $this->assertSame('discord', static::$factory::getIndex());
    }
}
