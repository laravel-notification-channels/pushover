<?php

namespace NotificationChannels\Pushover\Exceptions;

use Exception;

class ServiceCommunicationError extends Exception
{
    public static function communicationFailed(Exception $exception)
    {
        return new static("The communication with Pushover failed because `{$exception->getCode()} - {$exception->getMessage()}`");
    }
}
