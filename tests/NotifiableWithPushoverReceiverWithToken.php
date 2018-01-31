<?php

namespace NotificationChannels\Pushover\Test;

use NotificationChannels\Pushover\PushoverReceiver;

class NotifiableWithPushoverReceiverWithToken extends Notifiable
{
    public function routeNotificationFor($channel)
    {
        return PushoverReceiver::withUserKey('pushover-key')
            ->withApplicationToken('overridden-application-token')
            ->toDevice('iphone')
            ->toDevice('desktop');
    }
}
