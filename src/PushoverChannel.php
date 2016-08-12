<?php

namespace NotificationChannels\Pushover;

use NotificationChannels\Pushover\Events\MessageWasSent;
use NotificationChannels\Pushover\Events\SendingMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Pushover\Exceptions\CouldNotSendNotification;

class PushoverChannel
{
    /** @var Pushover */
    protected $pushover;

    /**
     * Create a new Pushover channel instance.
     *
     * @param  Pushover  $pushover
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
        $shouldSendMessage = event(new SendingMessage($notifiable, $notification), [], true) !== false;

        if (! $shouldSendMessage) {
            return;
        }

        if (! $pushoverKey = $notifiable->routeNotificationFor('pushover')) {
            return;
        }

        $message = $notification->toPushover($notifiable);

        try {


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
        catch(Exception $exception) {
            throw CouldNotSendNotification::serviceRespondedWithAnException($exception);
        }

        event(new MessageWasSent($notifiable, $notification));
    }
}
