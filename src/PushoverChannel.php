<?php

namespace NotificationChannels\Pushover;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Events\NotificationFailed;
use NotificationChannels\Pushover\Exceptions\ServiceCommunicationError;

class PushoverChannel
{
    /** @var Pushover */
    protected $pushover;

    /** @var Dispatcher */
    protected $events;

    /**
     * Create a new Pushover channel instance.
     *
     * @param  Pushover $pushover
     */
    public function __construct(Pushover $pushover, Dispatcher $events)
    {
        $this->pushover = $pushover;
        $this->events = $events;
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

        try {
            $this->pushover->send(array_merge($message->toArray(), [
                'user' => $pushoverKey,
            ]));
        } catch (ServiceCommunicationError $serviceCommunicationError) {
            $this->fireFailedEvent($notifiable, $notification, $serviceCommunicationError->getMessage());
        }
    }

    protected function fireFailedEvent($notifiable, $notification, $message)
    {
        $this->events->fire(
            new NotificationFailed($notifiable, $notification, 'pushover', [$message])
        );
    }
}
