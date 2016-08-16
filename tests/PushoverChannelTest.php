<?php

namespace NotificationChannels\Pushover\Test;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Notification;
use Mockery;
use NotificationChannels\Pushover\PushoverChannel;
use NotificationChannels\Pushover\PushoverMessage;
use NotificationChannels\Pushover\Pushover;
use Orchestra\Testbench\TestCase;

class PushoverChannelTest extends TestCase
{
    /** @var PushoverChannel */
    protected $channel;

    /** @var Pushover */
    protected $pushover;

    /** @var Notification */
    protected $notification;

    /** @var PushoverMessage */
    protected $message;

    /** @var Dispatcher */
    protected $events;

    public function setUp()
    {
        parent::setUp();

        $this->pushover = Mockery::mock(Pushover::class);
        $this->channel = new PushoverChannel($this->pushover);
        $this->notification = Mockery::mock(Notification::class);
        $this->message = Mockery::mock(PushoverMessage::class);
    }

    /** @test */
    public function it_can_send_a_message_to_pushover()
    {
        $notifiable = new Notifiable;

        $this->notification->shouldReceive('toPushover')
            ->with($notifiable)
            ->andReturn($this->message);
        $this->pushover->shouldReceive('send')
            ->with(Mockery::subset([
                'user' => 'pushover-key',
            ]));

        $this->channel->send($notifiable, $this->notification);
    }

    /** @test */
    public function it_fires_events_while_sending_a_message()
    {
        $this->notification->shouldReceive('toPushover')->andReturn($this->message);
        $this->pushover->shouldReceive('send');

        $this->channel->send(new Notifiable, $this->notification);
    }

    /** @test */
    public function it_does_not_send_a_message_when_notifiable_does_not_have_route_notificaton_for_pushover()
    {
        $this->notification->shouldReceive('toPushover')->never();

        $this->channel->send(new NotifiableWithoutRouteNotificationForPushover, $this->notification);
    }
}

class NotifiableWithoutRouteNotificationForPushover extends Notifiable
{
    public function routeNotificationFor($channel)
    {
        return false;
    }
}
