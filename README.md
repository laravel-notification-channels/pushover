# Pushover notifications channel for Laravel 5.3

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-notification-channels/pushover.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/pushover)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/laravel-notification-channels/pushover/master.svg?style=flat-square)](https://travis-ci.org/laravel-notification-channels/pushover)
[![StyleCI](https://styleci.io/repos/65543497/shield)](https://styleci.io/repos/65543497)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/811b5272-2311-4c3b-a445-997bbab7d66d.svg?style=flat-square)](https://insight.sensiolabs.com/projects/811b5272-2311-4c3b-a445-997bbab7d66d)
[![Quality Score](https://img.shields.io/scrutinizer/g/laravel-notification-channels/pushover.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/pushover)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-notification-channels/pushover.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/pushover)

This package makes it easy to send Pushover notifications with Laravel 5.3.

## Contents

- [Installation](#installation)
	- [Setting up your Pushover account](#setting-up-your-pushover-account)
- [Usage](#usage)
	- [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

## Installation

You can install the package via composer:

``` bash
composer require laravel-notification-channels/pushover
```

You must install the service provider:

```php
// config/app.php
'providers' => [
    ...
    NotificationChannels\Pushover\PushoverServiceProvider::class,
],
```

### Setting up your Pushover account

To start sending messages via Pushover, you have to [register an application](https://pushover.net/apps/build).
Add the generated Pushover application token to the services config file:
```php
// config/services.php
...
'pushover' => [
    'token' => 'YOUR_APPLICATION_TOKEN',
],
...
```

## Usage

Now you can use the channel in your `via()` method inside the notification as well as send a push notification:

``` php
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

Make sure there is a `routeNotificationForPushover` method on your notifiable model, for instance:
``` php
...
public function routeNotificationForPushover()
{
    return $this->pushover;
}
```

### Available Message methods
Please note that only the message content is mandatory, all other methods are optional. The message content can be set via `content('')`, via the create method `PushoverMessage::create('')` or via the constructor `new PushoverMessage('')`.

- `content($message)`: Accepts a string value for the message text.
- `title($title)`: Accepts a string value for the message title.
- `time($timestamp)`: Accepts either a `Carbon` object or an UNIX timestamp.
- `url($url[, $title])`: Accepts a string value for a [supplementary url](https://pushover.net/api#urls) and an optional string value for the title of the url.
- `sound($sound)`: Accepts a string value for the [notification sound](https://pushover.net/api#sounds).
- `priority($priority[, $retryTimeout, $expireAfter])`: Accepts an integer value for the priority and, when the priority is set to emergency, also an integer value for the retry timeout and expiry time (in seconds). Priority values are available as constants: `LOWEST_PRIORITY`, `LOW_PRIORITY`, `NORMAL_PRIORITY` and `EMERGENCY_PRIORITY`.
- `lowestPriority()`: Sets the priority to the lowest priority.
- `lowPriority()`: Sets the priority to low.
- `normalPriority()`: Sets the priority to normal.
- `highPriority()`: Sets the priority to high.
- `emergencyPriority($retryTimeout, $expireAfter)`: Sets the priority to emergency and accepts integer values for the retry timeout and expiry time (in seconds).


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing
    
``` bash
$ composer test
```

## Security

If you discover any security related issues, please email mail@casperboone.nl instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Casper Boone](https://github.com/casperboone)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
