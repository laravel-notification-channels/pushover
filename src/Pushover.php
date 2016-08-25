<?php

namespace NotificationChannels\Pushover;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use NotificationChannels\Pushover\Exceptions\CouldNotSendNotification;
use NotificationChannels\Pushover\Exceptions\ServiceCommunicationError;

class Pushover
{
    /**
     * Location of the Pushover API.
     *
     * @var string
     */
    protected $pushoverApiUrl = 'https://api.pushover.net/1/messages.json';

    /**
     * The HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * Pushover App Token.
     *
     * @var string
     */
    protected $token;

    /**
     * @param  HttpClient  $http
     * @param  string $token
     */
    public function __construct(HttpClient $http, $token)
    {
        $this->http = $http;

        $this->token = $token;
    }

    /**
     * Send Pushover message.
     *
     * @link  https://pushover.net/api
     *
     * @param  array  $params
     * @return \Psr\Http\Message\ResponseInterface
     * @throws CouldNotSendNotification
     */
    public function send($params)
    {
        try {
            return $this->http->post($this->pushoverApiUrl, [
                'form_params' => $this->paramsWithToken($params),
            ]);
        } catch (RequestException $exception) {
            if ($exception->getResponse()) {
                throw CouldNotSendNotification::serviceRespondedWithAnError($exception->getResponse());
            }
            throw ServiceCommunicationError::serviceCommunicationError($exception);
        } catch (Exception $exception) {
            throw ServiceCommunicationError::serviceCommunicationError($exception);
        }
    }

    /**
     * Merge token into parameters array.
     *
     * @param  array  $params
     * @return array
     */
    protected function paramsWithToken($params)
    {
        return array_merge($params, [
            'token' => $this->token,
        ]);
    }
}
