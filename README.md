# REST-Request-PHP

## What is it?

A REST client for PHP made to work with [BookingSync][bs]'s [REST API V3][api] and OAuth 2

## Supported PHP version

The default library is made for PHP 5.2 and upward.

Users of PHP 4 can use the compatible set of libraries from the PHP4 folder. Even if this libraries are upward compatible the default version if recommended when ever possible.

## Usage

Example of usage can be found in the [examples][examples]

Our examples are using [oauth2 bookingsync php](https://github.com/BookingSync/oauth2-bookingsync-php). PHP 5.4 is required here.

You might also want to read our [Getting Started with PHP Guide](http://developers.bookingsync.com/guides/getting-started-with-php/)

## Install

Note: Make sure to have the `cacert.pem` file in the same folder than `RestRequest.php`

## License

This code is free to be used under the terms of the [MIT license][mit].

## Authors

* [Sébastien Grosjean][zencocoon]

[api]: http://developers.bookingsync.com/
[examples]: https://github.com/BookingSync/rest-request-php/tree/master/examples/api-v3
[bs]:  http://www.bookingsync.com
[mit]:http://www.opensource.org/licenses/mit-license.php
[i]:  https://github.com/BookingSync/rest-request-php/issues
[zencocoon]: https://github.com/ZenCocoon
