<?php

// Useful resources:
//
// http://www.php.net/manual/en/function.json-decode.php
// http://www.php.net/manual/en/language.types.array.php

require_once '../../class/RestRequest.php';
// require_once '../class/JSON_support.php'; // Only required if using the PHP4 version

$api_token = 'YOUR_API_TOKEN'; // can be found in your profile: https://www.bookingsync.com/en/profile#api_token
$rental_id = 'YOUR_RENTAL_ID';

// Delete a rental: http://www.bookingsync.com/en/documentation/api/rentals#rentals-destroy
$request = new RestRequest('https://www.bookingsync.com/rentals/' . $rental_id . '.json', 'DELETE');
$request->setApiToken($api_token);
$request->execute();

// echo '<pre>' . print_r($request, true) . '</pre>';
var_dump($request->getDecodedResponse());
?>
