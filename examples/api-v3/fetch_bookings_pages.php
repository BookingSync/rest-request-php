<?php

// Useful resources:
//
// http://www.php.net/manual/en/function.json-decode.php
// http://www.php.net/manual/en/language.types.array.php

require './vendor/autoload.php';
require_once '../../class/RestRequest.php';
// require_once '../../class/JSON_support.php'; // Only required if using the PHP4 version

# Application credential can be found at https://www.bookingsync.com/en/partners/applications
# once you've created your first BookingSync application.

// Retrieved from a securely stored location.
// See https://github.com/BookingSync/rest-request-php/blob/master/examples/api-v3/authorize.php for auth part.
$refreshToken = 'XXXXXXXX';

$provider = new Bookingsync\OAuth2\Client\Provider\Bookingsync([
    'clientId'          => 'XXXXXXXX',
    'clientSecret'      => 'XXXXXXXX',
    'redirectUri'       => 'https://localhost/fetch_bookings_pages.php',
    'scopes'            => ['public'] // scopes required by your BookingSync application.
]);

$grant = new \League\OAuth2\Client\Grant\RefreshToken();
$token = $provider->getAccessToken($grant, ['refresh_token' => $refreshToken]);

// Get bookings first page: http://developers.bookingsync.com/reference/endpoints/bookings/
$request = new RestRequest('https://www.bookingsync.com/api/v3/bookings', 'GET');
$request->setAccessToken($token->accessToken);
$request->execute();

echo '<pre>' . print_r($request, true) . '</pre>';

$bookings = [];

for ($i = 1; $i <= $request->getResponseHeader()["X-Total-Pages"]; $i++) {
    if($i == 1) {
        $links = $request->getDecodedResponse()["links"];
        $bookings = $request->getDecodedResponse()["bookings"];
        $meta = $request->getDecodedResponse()["meta"];
    } else {
        $request = new RestRequest('https://www.bookingsync.com/api/v3/bookings?page='.$i, 'GET');
        $request->setAccessToken($token->accessToken);
        $request->execute();

        $bookings = array_merge($bookings, $request->getDecodedResponse()["bookings"]);
    }
}

// Contains all bookings 
$results = ["links" => $links, "bookings" => $bookings, "meta" => $meta];

echo '<pre>';
var_dump($results);
echo '</pre>';


?>
