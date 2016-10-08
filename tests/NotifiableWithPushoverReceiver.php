<?php

namespace NotificationChannels\Pushover\Test;

use NotificationChannels\Pushover\PushoverReceiver;

class NotifiableWithPushoverReceiver extends Notifiable
{
    public function routeNotificationFor($channel)
    {
        return PushoverReceiver::withUserKey('pushover-key')
            ->toDevice('iphone')
            ->toDevice('desktop');
    }
}
