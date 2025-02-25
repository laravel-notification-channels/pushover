<?php

namespace NotificationChannels\Pushover\Test;

use Carbon\Carbon;
use NotificationChannels\Pushover\Exceptions\EmergencyNotificationRequiresRetryAndExpire;
use NotificationChannels\Pushover\PushoverMessage;
use Orchestra\Testbench\TestCase;

class PushoverMessageTest extends TestCase
{
    /** @var PushoverMessage */
    protected $message;

    public function setUp(): void
    {
        parent::setUp();
        $this->message = new PushoverMessage();
    }

    /** @test */
    public function it_can_accept_a_message_when_constructing_a_message(): void
    {
        $message = new PushoverMessage('message text');

        $this->assertEquals('message text', $message->content);
    }

    /** @test */
    public function it_can_create_a_message(): void
    {
        $message = PushoverMessage::create();

        $this->assertInstanceOf(PushoverMessage::class, $message);
    }

    /** @test */
    public function it_can_accept_a_message_when_creating_a_message(): void
    {
        $message = PushoverMessage::create('message text');

        $this->assertEquals('message text', $message->content);
    }

    /** @test */
    public function it_can_set_content(): void
    {
        $this->message->content('message text');

        $this->assertEquals('message text', $this->message->content);
    }

    /** @test */
    public function it_can_set_the_message_format_to_plain(): void
    {
        $this->message->plain();

        $this->assertEquals(PushoverMessage::FORMAT_PLAIN, $this->message->format);
    }

    /** @test */
    public function it_can_set_the_message_format_to_html(): void
    {
        $this->message->html();

        $this->assertEquals(PushoverMessage::FORMAT_HTML, $this->message->format);
    }

    /** @test */
    public function it_can_set_the_message_format_to_monospace(): void
    {
        $this->message->monospace();

        $this->assertEquals(PushoverMessage::FORMAT_MONOSPACE, $this->message->format);
    }

    /** @test */
    public function it_can_set_a_title(): void
    {
        $this->message->title('message title');

        $this->assertEquals('message title', $this->message->title);
    }

    /** @test */
    public function it_can_set_a_time(): void
    {
        $this->message->time(123456789);

        $this->assertEquals(123456789, $this->message->timestamp);
    }

    /** @test */
    public function it_can_set_a_time_from_a_carbon_object(): void
    {
        $carbon = Carbon::now();

        $this->message->time($carbon);

        $this->assertEquals($carbon->timestamp, $this->message->timestamp);
    }

    /** @test */
    public function it_can_set_an_url(): void
    {
        $this->message->url('http://example.com');

        $this->assertEquals('http://example.com', $this->message->url);
    }

    /** @test */
    public function it_can_set_an_url_with_a_title(): void
    {
        $this->message->url('http://example.com', 'Go to example website');

        $this->assertEquals('http://example.com', $this->message->url);
        $this->assertEquals('Go to example website', $this->message->urlTitle);
    }

    /** @test */
    public function it_can_set_a_sound(): void
    {
        $this->message->sound('boing');

        $this->assertEquals('boing', $this->message->sound);
    }

    /** @test */
    public function it_can_set_a_priority(): void
    {
        $this->message->priority(PushoverMessage::NORMAL_PRIORITY);

        $this->assertEquals(0, $this->message->priority);
    }

    /** @test */
    public function it_can_set_a_priority_with_retry_and_expire(): void
    {
        $this->message->priority(PushoverMessage::EMERGENCY_PRIORITY, 60, 600);

        $this->assertEquals(2, $this->message->priority);
        $this->assertEquals(60, $this->message->retry);
        $this->assertEquals(600, $this->message->expire);
    }

    /** @test */
    public function it_cannot_set_priority_to_emergency_when_not_providing_a_retry_and_expiry_time(): void
    {
        $this->expectException(EmergencyNotificationRequiresRetryAndExpire::class);

        $this->message->priority(PushoverMessage::EMERGENCY_PRIORITY);
    }

    /** @test */
    public function it_can_set_the_priority_to_the_lowest(): void
    {
        $this->message->lowestPriority();

        $this->assertEquals(-2, $this->message->priority);
    }

    /** @test */
    public function it_can_set_the_priority_to_low(): void
    {
        $this->message->lowPriority();

        $this->assertEquals(-1, $this->message->priority);
    }

    /** @test */
    public function it_can_set_the_priority_to_normal(): void
    {
        $this->message->normalPriority();

        $this->assertEquals(0, $this->message->priority);
    }

    /** @test */
    public function it_can_set_the_priority_to_high(): void
    {
        $this->message->highPriority();

        $this->assertEquals(1, $this->message->priority);
    }

    /** @test */
    public function it_can_set_the_priority_to_emergency(): void
    {
        $this->message->emergencyPriority(60, 600);

        $this->assertEquals(2, $this->message->priority);
    }

    public function it_can_set_the_callback_url(): void
    {
        $callbackUrl = 'https://www.example.com/callback';

        $this->message->callback($callbackUrl);

        $this->assertEquals($callbackUrl, $this->message->callback);
    }
}
