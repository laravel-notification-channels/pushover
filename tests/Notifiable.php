<?php

namespace NotificationChannels\Pushover\Test;

class Notifiable
{
    public function routeNotificationFor($channel)
    {
        return 'pushover-key-30characters-long';
    }

    public function getKey()
    {
        return '1';
    }
}
