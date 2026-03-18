# Pushover Notifications Channel for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-notification-channels/pushover.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/pushover)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/github/actions/workflow/status/laravel-notification-channels/pushover/test.yml?style=flat-square)](https://github.com/laravel-notification-channels/pushover/actions)
[![StyleCI](https://styleci.io/repos/65543497/shield)](https://styleci.io/repos/65543497)
[![Quality Score](https://img.shields.io/scrutinizer/g/laravel-notification-channels/pushover.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/pushover)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/laravel-notification-channels/pushover/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/pushover/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-notification-channels/pushover.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/pushover)

This package makes it easy to send Pushover notifications with Laravel Notifications.

## Contents

- [Installation](#installation)
    - [Setting up your Pushover account](#setting-up-your-pushover-account)
- [Usage](#usage)
    - [Advanced usage and configuration](#advanced-usage-and-configuration)
    - [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

## Installation

You can install the package via composer:

```bash
composer require laravel-notification-channels/pushover
```

### Support Policy

This package supports various versions of Laravel and PHP with different releases. Choose the correct package version for your project.
Please note that older versions of this package are no longer being updated.

| Package Version | Laravel Version | PHP Version | Status |
|-----------------|-----------------|-------------|--------|
| 3.x             | 8 - 10          | 8.0 - 8.3   | EOL    |
| 4.x             | 8 - 12          | 8.1 - 8.4   | EOL    |
| 5.x             | 11 - 13         | 8.2 - 8.5   | Active |


### Setting up your Pushover account

To start sending messages via Pushover, you have to [register an application](https://pushover.net/apps/build).
Add the generated Pushover application token to the services config file:

```php
// config/services.php
'pushover' => [
    'token' => env('YOUR_APPLICATION_TOKEN'),
],
```

## Usage

Now you can use the channel in your `via()` method inside the notification as well as send a push notification:

```php
use NotificationChannels\Pushover\PushoverChannel;
use NotificationChannels\Pushover\PushoverMessage;
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [PushoverChannel::class];
    }

    public function toPushover($notifiable)
    {
        return PushoverMessage::create('The invoice has been paid.')
            ->title('Invoice paid')
            ->sound('incoming')
            ->lowPriority()
            ->url('http://example.com/invoices', 'Go to your invoices');
    }
}
```

To send Pushover notifications to the notifiable entity, add the `routeNotificationForPushover` method to that model. 
Usually, this is the User model. The `pushover_key` could be a database field and editable by the user itself.

```php
public function routeNotificationForPushover()
{
    return $this->pushover_key;
}
```

### Advanced usage and configuration

If you want to specify specific devices, you can return a `PushoverReceiver` object.

```php
public function routeNotificationForPushover() {
    return PushoverReceiver::withUserKey('pushover-key')
        ->toDevice('iphone')
        ->toDevice('desktop')
        // or, if you prefer:
        ->toDevice(['iphone', 'desktop']);
}
```

If you want to (dynamically) overrule the application token from the services config, e.g. because each user holds their
own application token, return a `PushoverReceiver` object like this:

```php
public function routeNotificationForPushover() {
    return PushoverReceiver::withUserKey('pushover-key')
        ->withApplicationToken('app-token');
}
```

You can also send a message to a Pushover group:

```php
public function routeNotificationForPushover() {
    return PushoverReceiver::withGroupKey('pushover-group-key');
}
```

### Available Message methods

| Method                                               | Description |
|------------------------------------------------------|-------------|
| `content($message)`                                  | Accepts a string value for the message text.  |
| `html()`                                             | Sets the message type to [HTML](https://pushover.net/api#html). |
| `monospace()`                                        | Sets the message type to monospace. |
| `plain()`                                            | Sets the message type to plain text, this is the default. |
| `title($title)`                                      | Accepts a string value for the message title. |
| `time($timestamp)`                                   | Accepts either a `Carbon` object or a UNIX timestamp. |
| `url($url[, $title])`                                | Accepts a string value for a [supplementary url](https://pushover.net/api#urls) and an optional string value for the title of the url. |
| `sound($sound)`                                      | Accepts a string value for the [notification sound](https://pushover.net/api#sounds). |
| `image($image)`                                      | Accepts a string value for the image location (either full or relative server path or a URL). If there is any error with the file (too big, not an image) it will silently send the message without the image attachment. |
| `priority($priority[, $retryTimeout, $expireAfter])` | Accepts an integer value for the priority and, when the priority is set to emergency, also an integer value for the retry timeout and expiry time (in seconds). Priority values are available as constants | `PushoverMessage::LOWEST_PRIORITY`, `PushoverMessage::LOW_PRIORITY`, `PushoverMessage::NORMAL_PRIORITY` and `PushoverMessage::EMERGENCY_PRIORITY`. |
| `lowestPriority()`                                   | Sets the priority to the lowest priority. |
| `lowPriority()`                                      | Sets the priority to low. |
| `normalPriority()`                                   | Sets the priority to normal. |
| `highPriority()`                                     | Sets the priority to high. |
| `emergencyPriority($retryTimeout, $expireAfter)`     | Sets the priority to emergency and accepts integer values for the retry timeout and expiry time (in seconds). |
| `callback($callbackUrl)`                             | Sets a publicly-accessible URL that Pushover will send a request to when the user has acknowledged your notification for an emergency notification. |

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
composer test
```

## Security

If you discover any security related issues, please email mail@casperboone.nl instead of using the issue tracker, or submit a vulnerability report using [GitHub Security Advisories for this package](https://github.com/laravel-notification-channels/pushover/security).

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Casper Boone](https://github.com/casperboone)
- [Kevin Woblick](https://github.com/kovah)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
