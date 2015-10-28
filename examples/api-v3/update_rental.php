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
$provider = new Bookingsync\OAuth2\Client\Provider\Bookingsync([
    'clientId'          => 'XXXXXXXX',
    'clientSecret'      => 'XXXXXXXX',
    'redirectUri'       => 'https://localhost/update_rental.php', // https is mandatory for BookingSync
    'scopes'            => ['public', 'rentals_write'] // scopes required by your BookingSync application.
]);

session_start();

if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();

    // Store the state in session. Used to mitigate CSRF attack
    $_SESSION['oauth2state'] = $provider->state;

    header('Location: '.$authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    try {

        $rental_id = "RENTAL_ID";
        $request = new RestRequest(
          'https://www.bookingsync.com/api/v3/rentals/' . $rental_id,
          'PUT',
          array("rentals" => [array(
            "name" => "NEW_RENTAL_NAME"
          )])
        );
        $request->setAccessToken($token->accessToken);
        $request->execute();

        echo '<pre>' . print_r($request, true) . '</pre>';
        echo '<pre>';
        var_dump($request->getDecodedResponse());
        echo '</pre>';

    } catch (Exception $e) {

        // Failed to get user details
        exit('Oh dear...');
    }
}
?>
