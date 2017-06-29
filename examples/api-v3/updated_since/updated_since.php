<?php

// Useful resources:
//
// http://www.php.net/manual/en/function.json-decode.php
// http://www.php.net/manual/en/language.types.array.php

require '../vendor/autoload.php';
require_once '../../../class/RestRequest.php';
// require_once '../../../class/JSON_support.php'; // Only required if using the PHP4 version

// Get last updated_since
$file = fopen('bookings_last_updated_at.txt', "r+");
$updated_since = fgets($file);

// Get bookings updated_since date from file: http://developers.bookingsync.com/reference/endpoints/rentals/
$request = new RestRequest('https://www.bookingsync.com/api/v3/bookings?updated_since='.$updated_since, 'GET');
$request->setAccessToken(FILL_YOUR_TOKEN_HERE);
$request->execute();

// If there are new bookings since updated_since
if ($request->getResponseHeader()['X-Total-Count'] >= 1) {

    // Get new updated_since from first page
    $new_updated_since = DateTime::createFromFormat('Y-m-d H:i:s e', $request->getResponseHeader()["x-updated-since-request-synced-at"])->format('Y-m-d\TH:i:s\Z');
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

    // Contains all bookings
    $results = ["links" => $links, "bookings" => $bookings, "meta" => $meta];

    fclose($file);

    echo '<pre>' . print_r($request, true) . '</pre>';
    echo '<pre>';
    var_dump($request->getDecodedResponse());
    echo '</pre>';
}

?>
