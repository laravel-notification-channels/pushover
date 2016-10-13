<?php

namespace NotificationChannels\Pushover\Test;

use NotificationChannels\Pushover\PushoverReceiver;

class PushoverReceiverTest extends TestCase
{
    private $pushoverReceiver;

    public function setUp()
    {
        $this->pushoverReceiver = PushoverReceiver::withUserKey('pushover-key');
    }

    /** @test */
    public function it_can_set_up_a_receiver_with_an_user_key()
    {
        $pushoverReceiver = PushoverReceiver::withUserKey('pushover-key');

        $this->assertArraySubset(['user' => 'pushover-key'], $pushoverReceiver->toArray());
    }

    /** @test */
    public function it_can_set_up_a_receiver_with_a_group_key()
    {
        $pushoverReceiver = PushoverReceiver::withGroupKey('pushover-key');

        $this->assertArraySubset(['user' => 'pushover-key'], $pushoverReceiver->toArray());
    }

    /** @test */
    public function it_can_add_a_single_device_to_the_receiver()
    {
        $this->pushoverReceiver->toDevice('iphone');

        $this->assertArraySubset(['device' => 'iphone'], $this->pushoverReceiver->toArray());
    }

    /** @test */
    public function it_can_add_multiple_devices_to_the_receiver()
    {
        $this->pushoverReceiver->toDevice('iphone')
            ->toDevice('desktop')
            ->toDevice('macbook');

        $this->assertArraySubset(['device' => 'iphone,desktop,macbook'], $this->pushoverReceiver->toArray());
    }

    /** @test */
    public function it_can_add_an_array_of_devices_to_the_receiver()
    {
        $this->pushoverReceiver->toDevice(['iphone', 'desktop', 'macbook']);

        $this->assertArraySubset(['device' => 'iphone,desktop,macbook'], $this->pushoverReceiver->toArray());
    }
}
