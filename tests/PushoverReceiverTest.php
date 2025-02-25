<?php

namespace NotificationChannels\Pushover\Test;

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

        $this->assertEquals([
            'user' => 'pushover-key',
        ], $pushoverReceiver->toArray());
    }

    /** @test */
    public function it_can_set_up_a_receiver_with_a_group_key()
    {
        $pushoverReceiver = PushoverReceiver::withGroupKey('pushover-key');

        $this->assertEquals([
            'user' => 'pushover-key',
        ], $pushoverReceiver->toArray());
    }

    /** @test */
    public function it_can_set_up_a_receiver_with_an_application_token()
    {
        $pushoverReceiver = PushoverReceiver::withUserKey('pushover-key')->withApplicationToken('pushover-token');

        $this->assertEquals([
            'user' => 'pushover-key',
            'token' => 'pushover-token',
        ], $pushoverReceiver->toArray());
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

        $this->assertEquals([
            'user' => 'pushover-key',
            'device' => 'iphone',
        ], $this->pushoverReceiver->toArray());
    }

    /** @test */
    public function it_can_add_multiple_devices_to_the_receiver()
    {
        $this->pushoverReceiver
            ->toDevice('iphone')
            ->toDevice('desktop')
            ->toDevice('macbook');

        $this->assertEquals([
            'user' => 'pushover-key',
            'device' => 'iphone,desktop,macbook',
        ], $this->pushoverReceiver->toArray());
    }

    /** @test */
    public function it_can_add_an_array_of_devices_to_the_receiver()
    {
        $this->pushoverReceiver->toDevice(['iphone', 'desktop', 'macbook']);

        $this->assertEquals([
            'user' => 'pushover-key',
            'device' => 'iphone,desktop,macbook',
        ], $this->pushoverReceiver->toArray());
    }
}
