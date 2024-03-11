<?php

namespace NotificationChannels\Pushover;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use NotificationChannels\Pushover\Exceptions\CouldNotSendNotification;
use NotificationChannels\Pushover\Exceptions\ServiceCommunicationError;

class PushoverChannel
{
    protected Pushover $pushover;

    protected Dispatcher $events;

    /**
     * Create a new Pushover channel instance.
     *
     * @param Pushover   $pushover
     * @param Dispatcher $events
     */
    public function __construct(Pushover $pushover, Dispatcher $events)
    {
        $this->pushover = $pushover;
        $this->events = $events;
    }

    /**
     * Send the given notification.
     *
     * @param mixed        $notifiable
     * @param Notification $notification
     *
     * @throws CouldNotSendNotification
     * @throws GuzzleException
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        if (! $pushoverReceiver = $notifiable->routeNotificationFor('pushover')) {
            return;
        }

        if (is_string($pushoverReceiver)) {
            // From https://pushover.net/api:
            // "User and group identifiers are 30 characters long, ..."
            if (strlen($pushoverReceiver) !== 30) {
                throw CouldNotSendNotification::pushoverKeyHasWrongLength($notifiable);
            }

            $pushoverReceiver = PushoverReceiver::withUserKey($pushoverReceiver);
        }

        $message = $notification->toPushover($notifiable);

        try {
            $this->pushover->send(
                array_merge($message->toArray(), $pushoverReceiver->toArray()),
                $notifiable
            );
        } catch (ServiceCommunicationError $serviceCommunicationError) {
            $this->fireFailedEvent($notifiable, $notification, $serviceCommunicationError->getMessage());
        }
    }

    protected function fireFailedEvent($notifiable, $notification, $message): void
    {
        $this->events->dispatch(
            new NotificationFailed($notifiable, $notification, 'pushover', [$message])
        );
    }
}
