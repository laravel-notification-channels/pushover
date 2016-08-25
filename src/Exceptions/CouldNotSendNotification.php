<?php

namespace NotificationChannels\Pushover\Exceptions;

use Exception;
use Psr\Http\Message\ResponseInterface;

class CouldNotSendNotification extends Exception
{
    public static function serviceRespondedWithAnError(ResponseInterface $response)
    {
        $statusCode = $response->getStatusCode();

        $result = json_decode($response->getBody());

        if ($result && isset($result->errors)) {
            return new static('Pushover responded with an error ('.$statusCode.'): '.implode(', ', $result->errors));
        }

        return new static('Pushover responded with an error ('.$statusCode.').');
    }
}
