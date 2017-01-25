<?php

namespace NotificationChannels\Pushover\Test;

use Mockery;
use Exception;
use Orchestra\Testbench\TestCase;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Events\Dispatcher;
use NotificationChannels\Pushover\Pushover;
use NotificationChannels\Pushover\PushoverChannel;
use NotificationChannels\Pushover\PushoverMessage;
use Illuminate\Notifications\Events\NotificationFailed;
use NotificationChannels\Pushover\Exceptions\ServiceCommunicationError;

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
        $this->events = Mockery::mock(Dispatcher::class);
        $this->channel = new PushoverChannel($this->pushover, $this->events);
        $this->notification = Mockery::mock(Notification::class);
        $this->message = Mockery::mock(PushoverMessage::class);
        $this->message->shouldReceive('toArray')->andReturn([]);
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
                'device' => '',
            ]))
            ->once();

        $this->channel->send($notifiable, $this->notification);
    }

    /** @test */
    public function it_can_send_a_message_to_pushover_using_a_pushover_receiver()
    {
        $notifiable = new NotifiableWithPushoverReceiver;

        $this->notification->shouldReceive('toPushover')
            ->with($notifiable)
            ->andReturn($this->message);
        $this->pushover->shouldReceive('send')
            ->with(Mockery::subset([
                'user' => 'pushover-key',
                'device' => 'iphone,desktop',
            ]))
            ->once();

        $this->channel->send($notifiable, $this->notification);
    }

    /** @test */
    public function it_fires_a_notification_failed_event_when_the_communication_with_pushover_failed()
    {
        $this->notification->shouldReceive('toPushover')->andReturn($this->message);
        $this->pushover->shouldReceive('send')->andThrow(
            ServiceCommunicationError::communicationFailed(new Exception())
        );

        $this->events->shouldReceive('fire')->with(Mockery::type(NotificationFailed::class));

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
