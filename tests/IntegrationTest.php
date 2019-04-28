<?php

namespace NotificationChannels\Pushover\Test;

use Mockery;
use Orchestra\Testbench\TestCase;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Events\Dispatcher;
use NotificationChannels\Pushover\Pushover;
use NotificationChannels\Pushover\PushoverChannel;
use NotificationChannels\Pushover\PushoverMessage;

class IntegrationTest extends TestCase
{
    /** @var HttpClient */
    protected $guzzleClient;

    /** @var Notification */
    protected $notification;

    /** @var Dispatcher */
    protected $events;

    public function setUp(): void
    {
        parent::setUp();

        $this->guzzleClient = Mockery::mock(HttpClient::class);
        $this->events = Mockery::mock(Dispatcher::class);
        $this->notification = Mockery::mock(Notification::class);

        $this->ignoreEvents();
    }

    /** @test */
    public function it_can_send_a_pushover_notification_with_a_global_token()
    {
        $message = (new PushoverMessage('Message text'))
            ->title('Message title')
            ->emergencyPriority(60, 600)
            ->time(123456789)
            ->sound('boing')
            ->url('http://example.com', 'Example Website');

        $this->requestWillBeSentToPushoverWith([
            'token' => 'global-application-token',
            'user' => 'pushover-key',
            'device' => 'iphone,desktop',
            'message' => 'Message text',
            'title' => 'Message title',
            'priority' => 2,
            'retry' => 60,
            'expire' => 600,
            'timestamp' => 123456789,
            'sound' => 'boing',
            'url' => 'http://example.com',
            'url_title' => 'Example Website',
        ]);

        $pushover = new Pushover($this->guzzleClient, 'global-application-token');

        $channel = new PushoverChannel($pushover, $this->events);

        $this->notification->shouldReceive('toPushover')->andReturn($message);

        $channel->send(new NotifiableWithPushoverReceiver, $this->notification);
    }

    /** @test */
    public function it_can_send_a_pushover_notification_with_an_overridden_token()
    {
        $message = (new PushoverMessage('Message text'))
            ->title('Message title')
            ->emergencyPriority(60, 600)
            ->time(123456789)
            ->sound('boing')
            ->url('http://example.com', 'Example Website');

        $this->requestWillBeSentToPushoverWith([
            'token' => 'overridden-application-token',
            'user' => 'pushover-key',
            'device' => 'iphone,desktop',
            'message' => 'Message text',
            'title' => 'Message title',
            'priority' => 2,
            'retry' => 60,
            'expire' => 600,
            'timestamp' => 123456789,
            'sound' => 'boing',
            'url' => 'http://example.com',
            'url_title' => 'Example Website',
        ]);

        $pushover = new Pushover($this->guzzleClient, 'global-application-token');

        $channel = new PushoverChannel($pushover, $this->events);

        $this->notification->shouldReceive('toPushover')->andReturn($message);

        $channel->send(new NotifiableWithPushoverReceiverWithToken(), $this->notification);
    }

    protected function requestWillBeSentToPushoverWith($params)
    {
        $this->guzzleClient->shouldReceive('post')
            ->with('https://api.pushover.net/1/messages.json', [
                'form_params' => $params,
            ])
            ->once();
    }

    protected function ignoreEvents()
    {
        $dispatcher = Mockery::mock('Illuminate\Contracts\Events\Dispatcher');
        $dispatcher->shouldReceive('fire');
        app()->instance('events', $dispatcher);
    }
}
