<?php

namespace NotificationChannels\Pushover\Test;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Notification;
use Mockery;
use NotificationChannels\Pushover\Pushover;
use NotificationChannels\Pushover\PushoverChannel;
use NotificationChannels\Pushover\PushoverMessage;
use Orchestra\Testbench\TestCase;

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
    public function it_can_send_a_pushover_notification_with_a_global_token(): void
    {
        $message = (new PushoverMessage('Message text'))
            ->title('Message title')
            ->emergencyPriority(60, 600)
            ->time(123456789)
            ->sound('boing')
            ->url('http://example.com', 'Example Website');

        $this->requestWillBeSentToPushoverWith([
            'token' => 'global-application-token',
            'message' => 'Message text',
            'title' => 'Message title',
            'timestamp' => 123456789,
            'priority' => 2,
            'url' => 'http://example.com',
            'url_title' => 'Example Website',
            'sound' => 'boing',
            'retry' => 60,
            'expire' => 600,
            'html' => false,
            'monospace' => false,
            'user' => 'pushover-key-30characters-long',
            'device' => 'iphone,desktop',
        ]);

        $pushover = new Pushover($this->guzzleClient, 'global-application-token');

        $channel = new PushoverChannel($pushover, $this->events);

        $this->notification->shouldReceive('toPushover')->andReturn($message);

        $channel->send(new NotifiableWithPushoverReceiver, $this->notification);
    }

    /** @test */
    public function it_can_send_a_pushover_notification_with_an_overridden_token(): void
    {
        $message = (new PushoverMessage('Message <b>text</b>'))
            ->html()
            ->title('Message title')
            ->emergencyPriority(60, 600)
            ->time(123456789)
            ->sound('boing')
            ->url('http://example.com', 'Example Website');

        $this->requestWillBeSentToPushoverWith([
            'token' => 'overridden-application-token',
            'message' => 'Message <b>text</b>',
            'title' => 'Message title',
            'timestamp' => 123456789,
            'priority' => 2,
            'url' => 'http://example.com',
            'url_title' => 'Example Website',
            'sound' => 'boing',
            'retry' => 60,
            'expire' => 600,
            'html' => true,
            'monospace' => false,
            'user' => 'pushover-key-30characters-long',
            'device' => 'iphone,desktop',
        ]);

        $pushover = new Pushover($this->guzzleClient, 'global-application-token');

        $channel = new PushoverChannel($pushover, $this->events);

        $this->notification->shouldReceive('toPushover')->andReturn($message);

        $channel->send(new NotifiableWithPushoverReceiverWithToken(), $this->notification);
    }

    protected function requestWillBeSentToPushoverWith($params): void
    {
        $multipartData = array_map(
            fn($key, $value) => ['name' => $key, 'contents' => $value],
            array_keys($params),
            array_values($params)
        );

        $this->guzzleClient->shouldReceive('post')
            ->with('https://api.pushover.net/1/messages.json', [
                'multipart' => $multipartData,
            ])
            ->once();
    }

    protected function ignoreEvents(): void
    {
        $dispatcher = Mockery::mock('Illuminate\Contracts\Events\Dispatcher');
        $dispatcher->shouldReceive('dispatch');
        app()->instance('events', $dispatcher);
    }
}
