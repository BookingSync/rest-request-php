<?php

// This example is reserved to External Application users
// More information at http://www.bookingsync.com/en/documentation/api/synchronizers
//
// Useful resources:
//
// http://www.php.net/manual/en/function.json-decode.php
// http://www.php.net/manual/en/language.types.array.php

require_once '../../class/RestRequest.php';
// require_once '../class/JSON_support.php'; // Only required if using the PHP4 version

$api_token = ''; // can be found in your profile: https://www.bookingsync.com/en/profile#api_token
$rental_id = ''; // the rental ID from wich you want to load the synchronizers' setup page

$request = new RestRequest('https://www.bookingsync.com/rentals/' . $rental_id . '/extrenter.json', 'GET');
$request->setApiToken($api_token);
$request->execute();

// echo '<pre>' . print_r($request, true) . '</pre>';
$response = $request->getDecodedResponse();
$auth_token = $response["extrenter"]["authentication_token"];
?>
<iframe width="100%" height="600"
  src="https://www.bookingsync.com/en/embed/v1/synchronizers?auth_token=<?php print $auth_token ?>"
  frameborder="0" scrolling="auto"></iframe>
