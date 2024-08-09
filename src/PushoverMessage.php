<?php

namespace NotificationChannels\Pushover;

use Carbon\Carbon;
use NotificationChannels\Pushover\Exceptions\EmergencyNotificationRequiresRetryAndExpire;

class PushoverMessage
{
    /**
     * The text content of the message.
     *
     * @var string
     */
    public string $content;

    /**
     * The format of the message.
     *
     * @var int
     */
    public int $format = self::FORMAT_PLAIN;

    /**
     * The (optional) title of the message.
     *
     * @var string|null
     */
    public string|null $title = null;

    /**
     * The (optional) timestamp of the message.
     *
     * @var int|null
     */
    public int|null $timestamp = null;

    /**
     * The (optional) priority of the message.
     *
     * @var int|null
     */
    public int|null $priority = null;

    /**
     * The (optional) timeout between retries when sending a message
     * with an emergency priority. The timeout is in seconds.
     *
     * @var int|null
     */
    public int|null $retry = null;

    /**
     * The (optional) expire time of a message with an emergency priority.
     * The expire time is in seconds.
     *
     * @var int|null
     */
    public int|null $expire = null;

    /**
     * The (optional) supplementary url of the message.
     *
     * @var string|null
     */
    public string|null $url = null;

    /**
     * The (optional) supplementary url title of the message.
     *
     * @var string|null
     */
    public string|null $urlTitle = null;

    /**
     * The (optional) sound of the message.
     *
     * @var string|null
     */
    public string|null $sound = null;

    /**
     * The (optional) image to be attached to the message.
     *
     * @var string|null
     */
    public string|null $image = null;
    /**
     * The (optional) publicly-accessible url that Pushover will use to notify your system when a user
     * acknowledges an Emergency notification.
     *
     * @var string|null
     */
    public string|null $callback = null;

    /**
     * Message formats.
     */
    public const FORMAT_PLAIN = 0;
    public const FORMAT_HTML = 1;
    public const FORMAT_MONOSPACE = 2;

    /**
     * Message priorities.
     */
    public const LOWEST_PRIORITY = -2;
    public const LOW_PRIORITY = -1;
    public const NORMAL_PRIORITY = 0;
    public const HIGH_PRIORITY = 1;
    public const EMERGENCY_PRIORITY = 2;

    /**
     * @param  string  $content
     * @return static
     */
    public static function create(string $content = ''): static
    {
        return new static($content);
    }

    /**
     * @param  string  $content
     */
    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    /**
     * Set the content of the Pushover message.
     *
     * @param  string  $content
     * @return $this
     */
    public function content(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set the formatting type to plain text.
     *
     * @return $this
     */
    public function plain(): static
    {
        $this->format = static::FORMAT_PLAIN;

        return $this;
    }

    /**
     * Set the formatting type to HTML.
     *
     * @return $this
     */
    public function html(): static
    {
        $this->format = static::FORMAT_HTML;

        return $this;
    }

    /**
     * Set the formatting type to monospace.
     *
     * @return $this
     */
    public function monospace(): static
    {
        $this->format = self::FORMAT_MONOSPACE;

        return $this;
    }

    /**
     * Set the title of the Pushover message.
     *
     * @param  string  $title
     * @return $this
     */
    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set the time of the Pushover message.
     *
     * @param  int|Carbon  $time
     * @return $this
     */
    public function time(int|Carbon $time): static
    {
        if ($time instanceof Carbon) {
            $time = (int) $time->timestamp;
        }

        $this->timestamp = $time;

        return $this;
    }

    /**
     * Set a supplementary url for the Pushover message.
     *
     * @param  string  $url
     * @param  string  $title
     * @return $this
     */
    public function url(string $url, string $title = ''): static
    {
        $this->url = $url;
        $this->urlTitle = $title;

        return $this;
    }

    /**
     * Set the sound of the Pushover message.
     *
     * @param  string  $sound
     * @return $this
     */
    public function sound(string $sound): static
    {
        $this->sound = $sound;

        return $this;
    }

    /**
     * Set the image for attaching to the Pushover message. Either full or relative server path or a URL.
     *
     * @param  string  $image
     * @return $this
     */
    public function image(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Set the priority of the Pushover message.
     * Retry and expire are mandatory when setting the priority to emergency.
     *
     * @param  int  $priority
     * @param  int|null  $retryTimeout
     * @param  int|null  $expireAfter
     * @return $this
     *
     * @throws EmergencyNotificationRequiresRetryAndExpire
     */
    public function priority(int $priority, int|null $retryTimeout = null, int|null $expireAfter = null): static
    {
        $this->noEmergencyWithoutRetryOrExpire($priority, $retryTimeout, $expireAfter);

        $this->priority = $priority;
        $this->retry = $retryTimeout;
        $this->expire = $expireAfter;

        return $this;
    }

    /**
     * Set the priority of the Pushover message to the lowest priority.
     *
     * @return $this
     *
     * @throws EmergencyNotificationRequiresRetryAndExpire
     */
    public function lowestPriority(): static
    {
        return $this->priority(self::LOWEST_PRIORITY);
    }

    /**
     * Set the priority of the Pushover message to low.
     *
     * @return $this
     *
     * @throws EmergencyNotificationRequiresRetryAndExpire
     */
    public function lowPriority(): static
    {
        return $this->priority(self::LOW_PRIORITY);
    }

    /**
     * Set the priority of the Pushover message to normal.
     *
     * @return $this
     *
     * @throws EmergencyNotificationRequiresRetryAndExpire
     */
    public function normalPriority(): static
    {
        return $this->priority(self::NORMAL_PRIORITY);
    }

    /**
     * Set the priority of the Pushover message to high.
     *
     * @return $this
     *
     * @throws EmergencyNotificationRequiresRetryAndExpire
     */
    public function highPriority(): static
    {
        return $this->priority(self::HIGH_PRIORITY);
    }

    /**
     * Set the priority of the Pushover message to emergency.
     * Retry and expire are mandatory when setting the priority to emergency.
     *
     * @param  int  $retryTimeout
     * @param  int  $expireAfter
     * @return $this
     *
     * @throws EmergencyNotificationRequiresRetryAndExpire
     */
    public function emergencyPriority(int $retryTimeout, int $expireAfter): static
    {
        return $this->priority(self::EMERGENCY_PRIORITY, $retryTimeout, $expireAfter);
    }

    /**
     * Set the callback url used by pushover to let your system
     * know when a emergency notification has been acknowledged.
     *
     * @param  string  $url
     * @return $this
     */
    public function callback(string $url): static
    {
        $this->callback = $url;

        return $this;
    }

    /**
     * Array representation of Pushover Message.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'message' => $this->content,
            'title' => $this->title,
            'timestamp' => $this->timestamp,
            'priority' => $this->priority,
            'url' => $this->url,
            'url_title' => $this->urlTitle,
            'sound' => $this->sound,
            'image' => $this->image,
            'retry' => $this->retry,
            'expire' => $this->expire,
            'html' => $this->format === static::FORMAT_HTML,
            'monospace' => $this->format === static::FORMAT_MONOSPACE,
            'callback' => $this->callback,
        ];
    }

    /**
     * Ensure an emergency message has an retry and expiry time.
     *
     * @param  int  $priority
     * @param  int|null  $retry
     * @param  int|null  $expire
     *
     * @throws EmergencyNotificationRequiresRetryAndExpire
     */
    protected function noEmergencyWithoutRetryOrExpire(int $priority, int|null $retry, int|null $expire): void
    {
        if ($priority === self::EMERGENCY_PRIORITY && ($retry === null || $expire === null)) {
            throw new EmergencyNotificationRequiresRetryAndExpire();
        }
    }
}
