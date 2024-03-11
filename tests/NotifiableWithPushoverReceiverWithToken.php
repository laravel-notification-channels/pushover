<?php

namespace NotificationChannels\Pushover\Test;

use NotificationChannels\Pushover\PushoverReceiver;

class NotifiableWithPushoverReceiverWithToken extends Notifiable
{
    public function routeNotificationFor($channel)
    {
        return PushoverReceiver::withUserKey('pushover-key-30characters-long')
            ->withApplicationToken('overridden-application-token')
            ->toDevice('iphone')
            ->toDevice('desktop');
    }
}
