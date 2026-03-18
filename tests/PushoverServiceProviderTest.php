<?php

namespace NotificationChannels\Pushover\Test;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Config;
use Mockery;
use NotificationChannels\Pushover\Pushover;
use NotificationChannels\Pushover\PushoverChannel;
use NotificationChannels\Pushover\PushoverServiceProvider;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PushoverServiceProviderTest extends TestCase
{
    protected PushoverServiceProvider $provider;

    public function setUp(): void
    {
        parent::setUp();

        $this->provider = new PushoverServiceProvider($this->app);
    }

    #[Test]
    public function it_gives_an_instantiated_pushover_object_when_the_channel_asks_for_it(): void
    {
        Config::shouldReceive('get')->with('services.pushover.token', null)->once()->andReturn('test-token');
        Config::shouldReceive('get')->with('database.default')->andReturn('array');
        Config::shouldReceive('get')->with('database.connections.array')->andReturn(['driver' => 'array']);

        $this->app->when(PushoverChannel::class)->needs(Pushover::class)->give(function () {
            return new Pushover(Mockery::mock(HttpClient::class), 'test-token');
        });

        $this->provider->boot();

        $pushover = $this->app->get(PushoverChannel::class);
        $this->assertInstanceOf(PushoverChannel::class, $pushover);
    }
}
