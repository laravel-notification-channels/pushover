# Changelog

All notable changes to `pushover` will be documented in this file.

## 4.1.0 - 2025-02-25
- Add support for Laravel 12
- Add new callback parameter which can be used to supply a callback URL for emergency notifications by @stijnbernards in #64

## 4.0.0 - 2024-03-20
- High-Impact changes
  - The Pushover notification channel now requires at least PHP 8.1. PHP 8.0 and below are no longer supported as these versions are EOL.
- What's Changed
  - Length of Pushover API key is checked for the correct length by @boryn in #61.
  - fire() was replaced by dispatch() in PushoverChannel by @boryn in #59.
  - $notifiable was missing in the send() method for proper error reporting by @boryn in #60.
  - ext-exif was added as an optional requirement for image attachments.
  - Compatibility with Laravel 11, including update of tests.
  - Bump the minimum required PHP version to 8.1, as 8.0 is no longer maintained.
    - Refactoring of classes to match 8.1 coding features such as typed properties and params.

## 3.1.0 - 2023-04-10
- Add support for image attachment by @boryn in #49
- Add support for Laravel 10 by @SamuelNitsche in #53
- chore: style fixes by @atymic in #57

## 3.0.0 - 2020-11-13
- Add support for Laravel 8 by upgrading Guzzle
- Add new methods on PushoverMessage to set the message format type using html(), monospace() or plain() (see the README)

## 2.1.2 - 2020-09-10
- Add support for Laravel 8 (#43)

## 2.1.1 - 2020-03-17
- Add support for Laravel 7 (#40)

## 2.1.0 - 2019-11-16
- Add support for Laravel 6 (#37)

## 2.0.1 - 2019-08-26
- Remove direct Carbon dependency

## 2.0.0 - 2019-06-12
- Add support for Laravel 5.8 (PR #30)

## 1.2.3 - 2018-09-25
- Add support for Laravel 5.7 (PR #25)

## 1.2.2 - 2018-02-13
- Add support for Laravel 5.6

## 1.2.1 - 2018-01-31
- Add support for dynamic application tokens (PR #22)

## 1.2.0 - 2017-08-03
- Add support for Laravel 5.5 (including auto discovery)

## 1.1.0 - 2017-01-25
- Add support for Laravel 5.4

## 1.0.1 - 2016-10-08
- Send messages to groups or to specific devices using a `PushoverReceiver` object, see the README for instructions

## 1.0.0 - 2016-08-25
- First release :tada:
- Fire event (instead of exception) when communication with Pushover fails
- Add backwards compatibility for older Laravel versions (with `laravel-notification-channels/backport`)

## 0.0.2 - 2016-08-13
- Better exceptions
- Improved code quality

## 0.0.1 - 2016-08-12
- Experimental release
