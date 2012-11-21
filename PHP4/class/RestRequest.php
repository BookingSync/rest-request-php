<?php
// REST-Request-PHP
// ================
// A REST client for PHP made to work with BookingSync's JSON API and Basic Auth
//
// This version is made for PHP 4+ support.
// If support for PHP 5.2+ is possible please use the default version located
// https://github.com/BookingSync/rest-request-php
//
// NOTE: This code is highly inspired from: http://www.gen-x-design.com/archives/making-restful-requests-in-php/
class RestRequest {

	function RestRequest($url = null, $verb = 'GET', $requestBody = null) {
		$this->url				    = $url;
		$this->verb				    = $verb;
		$this->requestBody		= $requestBody;
		$this->requestLength	= 0;
		$this->authMethod     = CURLAUTH_BASIC;
    $this->timeout        = 10;
		$this->username			  = null;
		$this->password			  = null;
		$this->acceptType		  = 'application/json';
		$this->contentType    = 'application/json';
		$this->responseBody		= null;
		$this->responseInfo		= null;
		$this->ch             = null;

		if ($this->requestBody !== null) {
			$this->buildPostBody();
		}
	}

	function flush() {
		$this->requestBody		= null;
		$this->requestLength	= 0;
		$this->verb				    = 'GET';
		$this->responseBody		= null;
		$this->responseInfo		= null;
	}

	function execute() {
		$this->ch = curl_init();
		$this->setAuth($ch);

		switch (strtoupper($this->verb)) {
			case 'GET':
				$this->executeGet();
				break;
			case 'POST':
				$this->executePost();
				break;
			case 'PUT':
				$this->executePut();
				break;
			case 'DELETE':
				$this->executeDelete();
				break;
			default:
				handleError('Current verb (' . $this->verb . ') is an invalid REST verb.');
		}
	}

	function handleError($message) {
	  curl_close($this->ch);
	  print $message;
	}

	function buildPostBody($data = null) {
		$data = ($data !== null) ? $data : $this->requestBody;

		if (!is_array($data)) {
			handleError('Invalid data input for postBody.  Array expected');
		}

    $data = json_encode($data);
		$this->requestBody = $data;
	}

	function executeGet() {
		$this->doExecute();
	}

	function executePost() {
		if (!is_string($this->requestBody)) {
			$this->buildPostBody();
		}

    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->requestBody);
    curl_setopt($this->ch, CURLOPT_POST, 1);

		$this->doExecute();
	}

	function executePut() {
		if (!is_string($this->requestBody)) {
			$this->buildPostBody();
		}

    curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->requestBody);

		$this->doExecute();
	}

	function executeDelete() {
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

		$this->doExecute();
	}

	function doExecute() {
		$this->setCurlOpts($this->ch);
		$this->responseBody = curl_exec($this->ch);
		$this->responseInfo	= curl_getinfo($this->ch);

		curl_close($this->ch);
	}

	function setCurlOpts() {
		curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($this->ch, CURLOPT_URL, $this->url);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
		  'Accept: ' . $this->acceptType,
		  'Content-Type: ' . $this->contentType
		));
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($this->ch, CURLOPT_CAINFO, dirname(__FILE__).'/cacert.pem');
	}

	function setAuth() {
		if ($this->username !== null && $this->password !== null)
		{
			curl_setopt($this->ch, CURLOPT_HTTPAUTH, $this->authMethod);
			curl_setopt($this->ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
		}
	}

	function getAcceptType() {
		return $this->acceptType;
	}

	function setAcceptType($acceptType) {
		$this->acceptType = $acceptType;
	}

  function getResponseBody() {
		return $this->responseBody;
	}

  /**
   * Requires external JSON decoder such as Services_JSON, PEAR, etc
   */
  function getDecodedResponse () {
    if (function_exists('json_decode')) {
      return json_decode($this->responseBody, true);
    } else {
      return $this->responseBody;
    }
  }

	function getResponseInfo() {
		return $this->responseInfo;
	}

	function getUrl() {
		return $this->url;
	}

	function setUrl($url) {
		$this->url = $url;
	}

	function setApiToken($apiToken) {
		$this->username = $apiToken;
		$this->password = 'X';
	}

	function getVerb() {
		return $this->verb;
	}

	function setVerb($verb) {
		$this->verb = $verb;
	}

  function setTimeout($timeout) {
    $this->timeout = $timeout;
  }
}
?>