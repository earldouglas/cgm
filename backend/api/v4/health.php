<?php

  function getHealthJson() {

    require_once dirname(__FILE__) . '/../../lib/health.php';

    $health = getHealth();

    $json =
      json_encode(
        $health,
        JSON_PRETTY_PRINT
      );

    return $json;
  }

  if (isset($_SERVER['REQUEST_METHOD'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      echo getHealthJson();
    }
  }

  if (getenv('TEST') !== false) {

    require_once dirname(__FILE__) . '/../../test/assert.php';
    require_once dirname(__FILE__) . '/../../lib/curl.php';

    $response = get('http://localhost:8888/api/v4/health');
    $health = json_decode($response, true);

    $name = 'Health check';
    $expected = 'ok';
    $observed = $health['db'];

    assertEquals($name, $expected, $observed);
  }

?>
