<?php

namespace NotificationChannels\Pushover;

class PushoverReceiver
{
    protected $key;
    protected $token;
    protected $devices = [];

    /**
     * PushoverReceiver constructor.
     *
     * @param  $key  User or group key.
     */
    protected function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Create new Pushover receiver with an user key.
     *
     * @param  $userKey  Pushover user key.
     * @return PushoverReceiver
     */
    public static function withUserKey($userKey)
    {
        return new static($userKey);
    }

    /**
     * Create new Pushover receiver with a group key.
     *
     * @param  $groupKey  Pushover group key.
     * @return PushoverReceiver
     */
    public static function withGroupKey($groupKey)
    {
        // This has exactly the same behaviour as an user key, so we
        // will use the same factory method as for the user key.
        return self::withUserKey($groupKey);
    }

    /**
     * Send the message to a specific device.
     *
     * @param  array|string  $device
     * @return PushoverReceiver
     */
    public function toDevice($device)
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
     * @param $token
     * @return PushoverReceiver
     */
    public function withApplicationToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get array representation of Pushover receiver.
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge([
            'user' => $this->key,
            'device' => implode(',', $this->devices),
        ], $this->token ? ['token' => $this->token] : []);
    }
}
