<?php

namespace NotificationChannels\Pushover\Test;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Config;
use Mockery;
use NotificationChannels\Pushover\Pushover;
use NotificationChannels\Pushover\PushoverChannel;
use NotificationChannels\Pushover\PushoverServiceProvider;
use Orchestra\Testbench\TestCase;

class PushoverServiceProviderTest extends TestCase
{
    /** @var PushoverServiceProvider */
    protected $provider;

    /** @var Application */
    protected $app;

    public function setUp(): void
    {
        parent::setUp();

        $this->app = Mockery::mock(Application::class);
        $this->provider = new PushoverServiceProvider($this->app);

        $this->app->shouldReceive('flush');
    }

    /** @test */
    public function it_gives_an_instantiated_pushover_object_when_the_channel_asks_for_it(): void
    {
        Config::shouldReceive('get')->with('services.pushover.token', null)->once()->andReturn('test-token');

        $this->app->shouldReceive('when')->with(PushoverChannel::class)->once()->andReturn($this->app);
        $this->app->shouldReceive('needs')->with(Pushover::class)->once()->andReturn($this->app);
        $this->app->shouldReceive('give')->with(Mockery::on(function ($pushover) {
            return $pushover() instanceof Pushover;
        }))->once();

        $this->provider->boot();
    }
}
