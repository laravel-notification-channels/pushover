<?php

namespace NotificationChannels\Pushover;

use Illuminate\Notifications\Notification;

class PushoverChannel
{
    /** @var Pushover */
    protected $pushover;

    /**
     * Create a new Pushover channel instance.
     *
     * @param  Pushover $pushover
     */
    public function __construct(Pushover $pushover)
    {
        $this->pushover = $pushover;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws \NotificationChannels\Pushover\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $pushoverKey = $notifiable->routeNotificationFor('pushover')) {
            return;
        }

        $message = $notification->toPushover($notifiable);

        $this->pushover->send([
            'user' => $pushoverKey,
            'message' => $message->content,
            'title' => $message->title,
            'timestamp' => $message->timestamp,
            'priority' => $message->priority,
            'url' => $message->url,
            'url_title' => $message->urlTitle,
            'sound' => $message->sound,
            'retry' => $message->retry,
            'expire' => $message->expire,
        ]);
    }
}
