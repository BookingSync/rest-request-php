<?php
// PHP support
// ================
// Add JSON support to PHP4, forward compatible with PHP 5.2+
//
// This code is coming from http://www.epigroove.com/posts/97/how_to_use_json_in_php_4_or_php_51x
//
if (!function_exists('json_decode')) {
  function json_decode($content, $assoc=false) {
    require_once 'JSON.php';

    if ($assoc) {
      $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
    }
    else {
      $json = new Services_JSON;
    }
    return $json->decode($content);
  }
}

if (!function_exists('json_encode')) {
  function json_encode($content) {
    require_once 'JSON.php';

    $json = new Services_JSON;
    return $json->encode($content);
  }
}
?>