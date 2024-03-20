<?php

namespace NotificationChannels\Pushover\Exceptions;

use Exception;
use Illuminate\Notifications\AnonymousNotifiable;
use Psr\Http\Message\ResponseInterface;

class CouldNotSendNotification extends Exception
{
    public static function serviceRespondedWithAnError(ResponseInterface $response, $notifiable): static
    {
        $statusCode = $response->getStatusCode();

        $result = json_decode($response->getBody()->getContents());

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

    public static function pushoverKeyHasWrongLength($notifiable): static
    {
        if ($notifiable instanceof AnonymousNotifiable) {
            return new static('Pushover key has wrong length. It needs to be 30 characters long.');
        }

        $exceptionMessage = sprintf(
            "Pushover key has wrong length for notifiable '%s' with id '%s'. It needs to be 30 characters long.",
            get_class($notifiable),
            $notifiable->getKey()
        );

        return new static($exceptionMessage);
    }
}
