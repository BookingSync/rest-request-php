<?php
// REST-Request-PHP
// ================
// A REST client for PHP made to work with BookingSync's JSON API V3 and OAuth 2 with Bearer Tokens
//
// NOTE: This code is highly inspired from: http://www.gen-x-design.com/archives/making-restful-requests-in-php/

class RestRequest
{
  protected $url;
  protected $verb;
  protected $requestBody;
  protected $requestLength;
  protected $accessToken;
  protected $acceptType;
  protected $responseBody;
  protected $responseInfo;

  public function __construct ($url = null, $verb = 'GET', $requestBody = null)
  {
    $this->url            = $url;
    $this->verb           = $verb;
    $this->requestBody    = $requestBody;
    $this->requestLength  = 0;
    $this->timeout        = 10;
    $this->accessToken    = null;
    $this->acceptType     = 'application/vnd.api+json';
    $this->contentType    = 'application/vnd.api+json';
    $this->responseBody   = null;
    $this->responseInfo   = null;

    if ($this->requestBody !== null)
    {
      $this->buildPostBody();
    }
  }

  public function flush ()
  {
    $this->requestBody    = null;
    $this->requestLength  = 0;
    $this->verb           = 'GET';
    $this->responseBody   = null;
    $this->responseInfo   = null;
  }

  public function execute ()
  {
    $ch = curl_init();

    try
    {
      switch (strtoupper($this->verb))
      {
        case 'GET':
          $this->executeGet($ch);
          break;
        case 'POST':
          $this->executePost($ch);
          break;
        case 'PUT':
          $this->executePut($ch);
          break;
        case 'DELETE':
          $this->executeDelete($ch);
          break;
        default:
          throw new InvalidArgumentException('Current verb (' . $this->verb . ') is an invalid REST verb.');
      }
    }
    catch (InvalidArgumentException $e)
    {
      curl_close($ch);
      throw $e;
    }
    catch (Exception $e)
    {
      curl_close($ch);
      throw $e;
    }

  }

  public function buildPostBody ($data = null)
  {
    $data = ($data !== null) ? $data : $this->requestBody;

    if (!is_array($data))
    {
      throw new InvalidArgumentException('Invalid data input for postBody.  Array expected');
    }

    $data = json_encode($data);
    $this->requestBody = $data;
  }

  protected function executeGet ($ch)
  {
    $this->doExecute($ch);
  }

  protected function executePost ($ch)
  {
    if (!is_string($this->requestBody))
    {
      $this->buildPostBody();
    }

    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->requestBody);
    curl_setopt($ch, CURLOPT_POST, 1);

    $this->doExecute($ch);
  }

  protected function executePut ($ch)
  {
    if (!is_string($this->requestBody))
    {
      $this->buildPostBody();
    }

    $this->requestLength = strlen($this->requestBody);

    $fh = fopen('php://memory', 'rw');
    fwrite($fh, $this->requestBody);
    rewind($fh);

    curl_setopt($ch, CURLOPT_INFILE, $fh);
    curl_setopt($ch, CURLOPT_INFILESIZE, $this->requestLength);
    curl_setopt($ch, CURLOPT_PUT, true);

    $this->doExecute($ch);

    fclose($fh);
  }

  protected function executeDelete ($ch)
  {
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

    $this->doExecute($ch);
  }

  protected function doExecute (&$curlHandle)
  {
    $this->setCurlOpts($curlHandle);
    $this->responseBody = curl_exec($curlHandle);
    $this->responseInfo = curl_getinfo($curlHandle);

    curl_close($curlHandle);
  }

  protected function setCurlOpts (&$curlHandle)
  {
    curl_setopt($curlHandle, CURLOPT_TIMEOUT, $this->timeout);
    curl_setopt($curlHandle, CURLOPT_URL, $this->url);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
      'Accept: ' . $this->acceptType,
      'Content-Type: ' . $this->contentType,
      'Authorization: Bearer ' . $this->accessToken
    ));
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curlHandle, CURLOPT_CAINFO, dirname(__FILE__).'/cacert.pem');
  }

  public function getAcceptType ()
  {
    return $this->acceptType;
  }

  public function setAcceptType ($acceptType)
  {
    $this->acceptType = $acceptType;
  }

  public function getResponseBody ()
  {
    return $this->responseBody;
  }

  public function getDecodedResponse ()
  {
    return json_decode($this->responseBody, true);
  }

  public function getResponseInfo ()
  {
    return $this->responseInfo;
  }

  public function getUrl ()
  {
    return $this->url;
  }

  public function setUrl ($url)
  {
    $this->url = $url;
  }

  public function setAccessToken ($accessToken)
  {
    $this->accessToken = $accessToken;
  }

  // For backward compatibility with BookingSync API v1
  public function setApiToken ($accessToken)
  {
    $this->setAccessToken($accessToken);
  }

  public function getVerb ()
  {
    return $this->verb;
  }

  public function setVerb ($verb)
  {
    $this->verb = $verb;
  }

  public function setTimeout ($timeout)
  {
    $this->timeout = $timeout;
  }
}
?>
