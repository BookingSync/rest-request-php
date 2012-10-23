<?php

// Useful resources:
//
// http://www.php.net/manual/en/function.json-decode.php
// http://www.php.net/manual/en/language.types.array.php

require_once '../class/RestRequest.php';
// require_once '../class/JSON_support.php'; // Only required if using the PHP4 version

$api_token = 'YOUR_API_TOKEN'; // can be found in your profile: https://www.bookingsync.com/en/profile#api_token

// Create a booking: http://www.bookingsync.com/en/documentation/api/bookings#bookings-create
$request = new RestRequest('https://www.bookingsync.com/bookings.json', 'POST',
  array("booking" => array(
    "booked"    => 1,
    "rental_id" => "YOUR_RENTAL_ID",
    "client_id" => "YOUR_CLIENT_ID",
    "start_at"  => "2012-09-10T16:00:00Z",
    "end_at"    => "2012-09-11T10:00:00Z"
  )));
$request->setApiToken($api_token);
$request->execute();

// echo '<pre>' . print_r($request, true) . '</pre>';
var_dump($request->getDecodedResponse());
?>