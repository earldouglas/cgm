<?php

  function getHealthJson() {

    require_once dirname(__FILE__) . '/../../l1-domain/health.php';
    require_once dirname(__FILE__) . '/../../l2-use-cases/healthcheck.php';
    require_once dirname(__FILE__) . '/../../l3-drivers/health.php';

    $systemHealth = new SystemHealth();
    $healthCheck = new HealthCheck($systemHealth);

    $json =
      json_encode(
        $healthCheck->getHealth(),
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
    require_once dirname(__FILE__) . '/../../test/http.php';

    $response = get('http://localhost:8888/api/v4/health');

    $name = 'Health check';
    $expected =
      stripMargin(
        '|{
         |    "db": "healthy"
         |}'
      );
    $observed = $response;

    assertEquals($name, $expected, $observed);
  }

?>
