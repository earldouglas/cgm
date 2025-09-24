<?php

  function get($url, $apiSecret) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('api-secret: ' . $apiSecret));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
  }

  function post($url, $apiSecret, $fields) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('api-secret: ' . $apiSecret));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
  }

  if (getenv('TEST') !== false) {

    require_once dirname(__FILE__) . '/../test/assert.php';

    $response = get('https://earldouglas.com', '');

    preg_match('/<title>([^<]*)<\/title>/', $response, $matches);

    $name = 'GET https://earldouglas.com';
    $expected = 'James Earl Douglas';
    $observed = $matches[1];

    assertEquals($name, $expected, $observed);
  }

?>
