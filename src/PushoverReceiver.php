<?php

namespace NotificationChannels\Pushover;

class PushoverReceiver
{
    protected string $key;
    protected string|null $token = null;
    protected array $devices = [];

    /**
     * PushoverReceiver constructor.
     *
     * @param string $key User or group key.
     */
    protected function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * Create new Pushover receiver with an user key.
     *
     * @param string $userKey Pushover user key.
     * @return PushoverReceiver
     */
    public static function withUserKey(string $userKey): PushoverReceiver
    {
        return new static($userKey);
    }

    /**
     * Create new Pushover receiver with a group key.
     *
     * @param string $groupKey Pushover group key.
     * @return PushoverReceiver
     */
    public static function withGroupKey(string $groupKey): PushoverReceiver
    {
        // This has exactly the same behaviour as an user key, so we
        // will use the same factory method as for the user key.
        return self::withUserKey($groupKey);
    }

    /**
     * Send the message to a specific device.
     *
     * @param array|string $device
     * @return PushoverReceiver
     */
    public function toDevice(array|string $device): static
    {
        if (is_array($device)) {
            $this->devices = array_merge($device, $this->devices);

            return $this;
        }

        $this->devices[] = $device;

        return $this;
    }

    /**
     * Set the application token.
     *
     * @param  $token
     * @return PushoverReceiver
     */
    public function withApplicationToken($token): static
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get array representation of Pushover receiver.
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            'user' => $this->key,
        ];

        if (!empty($this->devices)) {
            $data['device'] = implode(',', $this->devices);
        }

        if ($this->token) {
            $data['token'] = $this->token;
        }

        return $data;
    }
}
