<?php

namespace App\Tests\Movie\Notifier\Factory;

use App\Movie\Notifier\Factory\SlackNotificationFactory;
use App\Movie\Notifier\Notification\SlackNotification;
use PHPUnit\Framework\TestCase;

class SlackNotificationFactoryTest extends TestCase
{
    private static ?SlackNotificationFactory $factory = null;

    public static function setUpBeforeClass(): void
    {
        static::$factory = new SlackNotificationFactory();
    }

    /**
     * @group unit
     */
    public function testFactoryReturnsSlackNotificationObject(): void
    {
        $notification = static::$factory->createNotification('subject');

        $this->assertInstanceOf(SlackNotification::class, $notification);
        $this->assertSame('subject', $notification->getSubject());
    }

    /**
     * @group unit
     */
    public function testFactoryGetIndexMethodReturnsSlack()
    {
        $this->assertSame('slack', static::$factory::getIndex());
    }
}
