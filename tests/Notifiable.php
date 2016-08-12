<?php

namespace NotificationChannels\Pushover\Test;

class Notifiable
{
    public function routeNotificationFor($channel)
    {
        return 'pushover-key';
    }
}