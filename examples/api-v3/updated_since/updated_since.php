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
    'redirectUri'       => 'https://localhost/updated_since/updated_since.php', // https is mandatory for BookingSync
    'scopes'            => ['public'] // scopes required by your BookingSync application.
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

    $filename = 'bookings_last_updated_at.txt';

    // First time running script
    if (!file_exists($filename)) {

        // Get bookings : http://developers.bookingsync.com/reference/endpoints/rentals/
        $request = new RestRequest('https://www.bookingsync.com/api/v3/bookings', 'GET');
        $request->setAccessToken($token->accessToken);
        $request->execute();

        $updated_since = $request->getResponseHeader()["x-updated-since-request-synced-at"];
        $bookings = [];

        // Loop bookings pages
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

        // Save updated_since date in file or DB
        $file = fopen($filename, "w+");
        fputs($file, $updated_since);

    } else {

        // Get updated_since date from file or DB, you need ISO8601 date format
        $file = fopen($filename, "r+");
        $updated_since = DateTime::createFromFormat('Y-m-d H:i:s e', fgets($file))->format(DateTime::ISO8601);

        // Get bookings updated_since date from file: http://developers.bookingsync.com/reference/endpoints/rentals/
        $request = new RestRequest('https://www.bookingsync.com/api/v3/bookings?updated_since='.$updated_since, 'GET');
        $request->setAccessToken($token->accessToken);
        $request->execute();

        // If there are new bookings since updated_since
        if ($request->getResponseHeader()['X-Total-Count'] > 1) {

            $date = DateTime::createFromFormat('Y-m-d H:i:s e', $request->getResponseHeader()["x-updated-since-request-synced-at"]);
            $new_updated_since = $date->format(DateTime::ISO8601);
            $bookings = [];

            // Loop new bookings pages
            for ($i = 1; $i <= $request->getResponseHeader()["X-Total-Pages"]; $i++) {
                if($i == 1) {
                    $links = $request->getDecodedResponse()["links"];
                    $bookings = $request->getDecodedResponse()["bookings"];
                    $meta = $request->getDecodedResponse()["meta"];
                } else {
                    $request = new RestRequest('https://www.bookingsync.com/api/v3/bookings?updated_since='.$updated_since.'&page='.$i, 'GET');
                    $request->setAccessToken($token->accessToken);
                    $request->execute();

                    $bookings = array_merge($bookings, $request->getDecodedResponse()["bookings"]);
                }
            }

            // Save updated_since date in file or DB for the next time
            fputs($file, $new_updated_since);
        }
    }

    fclose($file);

    echo '<pre>' . print_r($request, true) . '</pre>';
    echo '<pre>';
    var_dump($request->getDecodedResponse());
    echo '</pre>';

}
?>
