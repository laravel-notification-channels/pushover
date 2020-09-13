<?php

namespace NotificationChannels\Pushover\Test;

use DMS\PHPUnitExtensions\ArraySubset\Assert;
use NotificationChannels\Pushover\PushoverReceiver;
use Orchestra\Testbench\TestCase;

class PushoverReceiverTest extends TestCase
{
    private $pushoverReceiver;

    public function setUp(): void
    {
        $this->pushoverReceiver = PushoverReceiver::withUserKey('pushover-key');
    }

    /** @test */
    public function it_can_set_up_a_receiver_with_an_user_key()
    {
        $pushoverReceiver = PushoverReceiver::withUserKey('pushover-key');

        Assert::assertArraySubset(['user' => 'pushover-key'], $pushoverReceiver->toArray());
    }

    /** @test */
    public function it_can_set_up_a_receiver_with_a_group_key()
    {
        $pushoverReceiver = PushoverReceiver::withGroupKey('pushover-key');

        Assert::assertArraySubset(['user' => 'pushover-key'], $pushoverReceiver->toArray());
    }

    /** @test */
    public function it_can_set_up_a_receiver_with_an_application_token()
    {
        $pushoverReceiver = PushoverReceiver::withUserKey('pushover-key')->withApplicationToken('pushover-token');

        Assert::assertArraySubset(['user' => 'pushover-key', 'token' => 'pushover-token'], $pushoverReceiver->toArray());
    }

    /** @test */
    public function it_only_exposes_app_token_when_set()
    {
        $pushoverReceiverUserKey = PushoverReceiver::withUserKey('pushover-key');
        $pushoverReceiverGroupKey = PushoverReceiver::withGroupKey('pushover-key');

        $this->assertArrayNotHasKey('token', $pushoverReceiverUserKey->toArray());
        $this->assertArrayNotHasKey('token', $pushoverReceiverGroupKey->toArray());
    }

    /** @test */
    public function it_can_add_a_single_device_to_the_receiver()
    {
        $this->pushoverReceiver->toDevice('iphone');

        Assert::assertArraySubset(['device' => 'iphone'], $this->pushoverReceiver->toArray());
    }

    /** @test */
    public function it_can_add_multiple_devices_to_the_receiver()
    {
        $this->pushoverReceiver->toDevice('iphone')
            ->toDevice('desktop')
            ->toDevice('macbook');

        Assert::assertArraySubset(['device' => 'iphone,desktop,macbook'], $this->pushoverReceiver->toArray());
    }

    /** @test */
    public function it_can_add_an_array_of_devices_to_the_receiver()
    {
        $this->pushoverReceiver->toDevice(['iphone', 'desktop', 'macbook']);

        Assert::assertArraySubset(['device' => 'iphone,desktop,macbook'], $this->pushoverReceiver->toArray());
    }
}
