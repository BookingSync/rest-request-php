# REST-Request-PHP

## What is it?

A REST client for PHP made to work with [BookingSync][bs]'s [REST API][api] and Basic Auth

## Supported PHP version

The default library is made for PHP 5.2 and upward.

Users of PHP 4 can use the compatible set of libraries from the PHP4 folder. Even if this libraries are upward compatible the default version if recommended when ever possible.

## Usage

    <?php
    require_once 'class/RestRequest.php';
    // require_once 'class/JSON_support.php'; // Only required if using the PHP4 version

    $account_id = 'YOUR_ACCOUNT_ID';

    // Get public bookings for the account: http://www.bookingsync.com/en/documentation/api/mybookings#mybookings-list
    $request = new RestRequest('https://www.bookingsync.com/mybookings/' . $account_id . '.json', 'GET');
    $request->execute();

    var_dump($request->getDecodedResponse());
    ?>

More example of usage can be found in the [examples][examples]

## License

This code is free to be used under the terms of the [MIT license][mit].

## Authors

* [Sébastien Grosjean][zencocoon]

[api]: http://www.bookingsync.com/en/documentation/api
[examples]: https://github.com/BookingSync/rest-request-php/tree/master/examples
[bs]:  http://www.bookingsync.com
[mit]:http://www.opensource.org/licenses/mit-license.php
[i]:  https://github.com/BookingSync/rest-request-php/issues
[zencocoon]: https://github.com/ZenCocoon