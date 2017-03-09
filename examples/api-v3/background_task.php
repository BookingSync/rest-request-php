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
    'scopes'            => ['public'] // scopes required by your BookingSync application.
]);
$grant = new \League\OAuth2\Client\Grant\RefreshToken();
$token = $provider->getAccessToken($grant, ['refresh_token' => $refreshToken]);

echo '<pre>';
var_dump($token);
echo '</pre>';

// Get rentals: http://developers.bookingsync.com/reference/endpoints/rentals/
$request = new RestRequest('https://www.bookingsync.com/api/v3/rentals', 'GET');
$request->setAccessToken($token->accessToken);
$request->execute();

echo '<pre>' . print_r($request, true) . '</pre>';
echo '<pre>';
var_dump($request->getDecodedResponse());
echo '</pre>';
?>
