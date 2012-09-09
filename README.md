# REST-Request-PHP

## What is it?

A REST client for PHP made to work with [BookingSync][bs]'s JSON [API][api] and Basic Auth

## Usage

    <?php
    require_once 'RestRequest.inc.php'

    $account_id = 'YOUR_ACCOUNT_ID';

    // Get public bookings for the account: http://www.bookingsync.com/en/documentation/api/mybookings#mybookings-list
    $request = new RestRequest('https://www.bookingsync.com/mybookings/' . $account_id . '.json', 'GET');
    $request->execute();

    var_dump(json_decode($request->getResponseBody()));
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