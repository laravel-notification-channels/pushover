<?php

namespace NotificationChannels\Pushover\Test;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use Mockery;
use NotificationChannels\Pushover\Exceptions\CouldNotSendNotification;
use NotificationChannels\Pushover\Exceptions\ServiceCommunicationError;
use NotificationChannels\Pushover\Pushover;
use Orchestra\Testbench\TestCase;

class PushoverTest extends TestCase
{
    /** @var Pushover */
    protected $pushover;

    /** @var HttpClient */
    protected $guzzleClient;

    public function setUp(): void
    {
        parent::setUp();

        $this->guzzleClient = Mockery::mock(HttpClient::class);
        $this->pushover = new Pushover($this->guzzleClient, 'new-application-token-12345678');
    }

    /** @test */
    public function it_can_send_a_request_to_pushover(): void
    {
        $this->guzzleClient->shouldReceive('post')
            ->with('https://api.pushover.net/1/messages.json', [
                'multipart' => [
                    ['name' => 'token', 'contents' => 'new-application-token-12345678'],
                ],
            ]);

        $this->pushover->send([], new Notifiable());

        $this->expectNotToPerformAssertions();
    }

    /** @test */
    public function it_can_send_a_request_with_an_overridden_token(): void
    {
        $this->guzzleClient->shouldReceive('post')
            ->with('https://api.pushover.net/1/messages.json', [
                'multipart' => [
                    ['name' => 'token', 'contents' => 'dynamic-application-token-1234'],
                ],
            ]);

        $this->pushover->send(['token' => 'dynamic-application-token-1234'], new Notifiable());

        $this->expectNotToPerformAssertions();
    }

    /** @test */
    public function it_can_accept_parameters_for_a_send_request(): void
    {
        $this->guzzleClient->shouldReceive('post')
            ->with('https://api.pushover.net/1/messages.json', [
                'multipart' => [
                    ['name' => 'token', 'contents' => 'new-application-token-12345678'],
                    ['name' => 'content', 'contents' => 'content of message'],
                ],
            ]);

        $this->pushover->send([
            'content' => 'content of message',
        ], new Notifiable());

        $this->expectNotToPerformAssertions();
    }

    /** @test */
    public function it_throws_an_exception_when_pushover_returns_an_error_with_invalid_json(): void
    {
        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage('Pushover responded with an error (400)');

        $guzzleRequest = Mockery::mock(\Psr\Http\Message\RequestInterface::class);
        $guzzleResponse = Mockery::mock(\Psr\Http\Message\ResponseInterface::class);
        $guzzleResponse->shouldReceive('getStatusCode')->andReturn(400);
        $guzzleResponse->shouldReceive('getBody->getContents');

        $this->guzzleClient->shouldReceive('post')->andThrow(new RequestException('message', $guzzleRequest, $guzzleResponse));

        $this->pushover->send([], new Notifiable());
    }

    /** @test */
    public function it_throws_an_exception_when_pushover_returns_an_error_with_valid_json(): void
    {
        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage("Pushover responded with an error (400) for notifiable 'NotificationChannels\Pushover\Test\Notifiable' with id '1': error_message_1, error_message_2");

        $guzzleRequest = Mockery::mock(\Psr\Http\Message\RequestInterface::class);
        $guzzleResponse = Mockery::mock(\Psr\Http\Message\ResponseInterface::class);
        $guzzleResponse->shouldReceive('getStatusCode')->andReturn(400);
        $guzzleResponse->shouldReceive('getBody->getContents')->andReturn('{"errors": ["error_message_1", "error_message_2"]}');

        $this->guzzleClient->shouldReceive('post')->andThrow(new RequestException('message', $guzzleRequest, $guzzleResponse));

        $this->pushover->send([], new Notifiable());
    }

    /** @test */
    public function it_throws_an_exception_when_pushover_returns_nothing(): void
    {
        $this->expectException(ServiceCommunicationError::class);
        $this->expectExceptionMessage('The communication with Pushover failed because');

        $guzzleRequest = Mockery::mock(\Psr\Http\Message\RequestInterface::class);

        $this->guzzleClient->shouldReceive('post')->andThrow(new RequestException('message', $guzzleRequest, null));

        $this->pushover->send([], new Notifiable());
    }

    /** @test */
    public function it_throws_an_exception_when_an_unknown_communication_error_occurred(): void
    {
        $this->expectException(ServiceCommunicationError::class);
        $this->expectExceptionMessage('The communication with Pushover failed');

        $this->guzzleClient->shouldReceive('post')->andThrow(new Exception);

        $this->pushover->send([], new Notifiable());
    }
}
