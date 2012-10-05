<?php

// Useful resources:
//
// http://www.php.net/manual/en/function.json-decode.php
// http://www.php.net/manual/en/language.types.array.php

require_once '../class/RestRequest.php';
// require_once '../class/JSON_support.php'; // Only required if using the PHP4 version

$account_id = 'YOUR_ACCOUNT_ID';

// Get public bookings for the account: http://www.bookingsync.com/en/documentation/api/mybookings#mybookings-list
$request = new RestRequest('https://www.bookingsync.com/mybookings/' . $account_id . '.json', 'GET');
$request->execute();

// echo '<pre>' . print_r($request, true) . '</pre>';
var_dump(json_decode($request->getResponseBody()));
?>