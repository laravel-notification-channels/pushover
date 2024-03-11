<?php

namespace NotificationChannels\Pushover\Exceptions;

use Exception;
use Psr\Http\Message\ResponseInterface;

class CouldNotSendNotification extends Exception
{
    public static function serviceRespondedWithAnError(ResponseInterface $response, $notifiable)
    {
        $statusCode = $response->getStatusCode();

        $result = json_decode($response->getBody());

        $exceptionMessage = sprintf(
            "Pushover responded with an error (%s) for notifiable '%s' with id '%s'",
            $statusCode,
            get_class($notifiable),
            $notifiable->getKey()
        );

        if ($result && isset($result->errors)) {
            $exceptionMessage = sprintf(
                "$exceptionMessage: %s",
                implode(', ', $result->errors)
            );
        }

        return new static($exceptionMessage, $statusCode);
    }

    public static function pushoverKeyHasWrongLength($notifiable)
    {
        $exceptionMessage = sprintf(
            "Pushover key has wrong length for notifiable '%s' with id '%s'. It needs to be 30 characters long.",
            get_class($notifiable),
            $notifiable->getKey()
        );

        return new static($exceptionMessage);
    }
}
