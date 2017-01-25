<?php

namespace NotificationChannels\Pushover\Test;

use Mockery;
use Exception;
use Orchestra\Testbench\TestCase;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use NotificationChannels\Pushover\Pushover;
use NotificationChannels\Pushover\Exceptions\CouldNotSendNotification;
use NotificationChannels\Pushover\Exceptions\ServiceCommunicationError;

class PushoverTest extends TestCase
{
    /** @var Pushover */
    protected $pushover;

    /** @var HttpClient */
    protected $guzzleClient;

    public function setUp()
    {
        parent::setUp();

        $this->guzzleClient = Mockery::mock(HttpClient::class);
        $this->pushover = new Pushover($this->guzzleClient, 'application-token');
    }

    /** @test */
    public function it_can_send_a_request_to_pushover()
    {
        $this->guzzleClient->shouldReceive('post')
            ->with('https://api.pushover.net/1/messages.json', [
                'form_params' => [
                    'token' => 'application-token',
                ],
            ]);

        $this->pushover->send([]);
    }

    /** @test */
    public function it_can_accept_parameters_for_a_send_request()
    {
        $this->guzzleClient->shouldReceive('post')
            ->with('https://api.pushover.net/1/messages.json', [
                'form_params' => [
                    'token' => 'application-token',
                    'content' => 'content of message',
                ],
            ]);

        $this->pushover->send([
            'content' => 'content of message',
        ]);
    }

    /** @test */
    public function it_throws_an_exception_when_pushover_returns_an_error_with_invalid_json()
    {
        $this->setExpectedException(CouldNotSendNotification::class, 'Pushover responded with an error (400).');

        $guzzleRequest = Mockery::mock(\Psr\Http\Message\RequestInterface::class);
        $guzzleResponse = Mockery::mock(\Psr\Http\Message\ResponseInterface::class);
        $guzzleResponse->shouldReceive('getStatusCode')->andReturn(400);
        $guzzleResponse->shouldReceive('getBody')->andReturn('');

        $this->guzzleClient->shouldReceive('post')->andThrow(new RequestException(null, $guzzleRequest, $guzzleResponse));

        $this->pushover->send([]);
    }

    /** @test */
    public function it_throws_an_exception_when_pushover_returns_an_error_with_valid_json()
    {
        $this->setExpectedException(CouldNotSendNotification::class, 'Pushover responded with an error (400): error_message_1, error_message_2');

        $guzzleRequest = Mockery::mock(\Psr\Http\Message\RequestInterface::class);
        $guzzleResponse = Mockery::mock(\Psr\Http\Message\ResponseInterface::class);
        $guzzleResponse->shouldReceive('getStatusCode')->andReturn(400);
        $guzzleResponse->shouldReceive('getBody')->andReturn('{"errors": ["error_message_1", "error_message_2"]}');

        $this->guzzleClient->shouldReceive('post')->andThrow(new RequestException(null, $guzzleRequest, $guzzleResponse));

        $this->pushover->send([]);
    }

    /** @test */
    public function it_throws_an_exception_when_pushover_returns_nothing()
    {
        $this->setExpectedException(ServiceCommunicationError::class, 'The communication with Pushover failed because');

        $guzzleRequest = Mockery::mock(\Psr\Http\Message\RequestInterface::class);

        $this->guzzleClient->shouldReceive('post')->andThrow(new RequestException(null, $guzzleRequest, null));

        $this->pushover->send([]);
    }

    /** @test */
    public function it_throws_an_exception_when_an_unknown_communication_error_occurred()
    {
        $this->setExpectedException(ServiceCommunicationError::class, 'The communication with Pushover failed');

        $this->guzzleClient->shouldReceive('post')->andThrow(new Exception);

        $this->pushover->send([]);
    }
}
