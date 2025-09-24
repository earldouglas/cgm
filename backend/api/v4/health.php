<?php

  function getHealthJson() {

    require_once dirname(__FILE__) . '/../../l1-domain/authz.php';
    require_once dirname(__FILE__) . '/../../l1-domain/health.php';
    require_once dirname(__FILE__) . '/../../l2-use-cases/healthcheck.php';
    require_once dirname(__FILE__) . '/../../l3-drivers/health.php';
    require_once dirname(__FILE__) . '/../../l4-http/authz.php';

    $systemHealth = new SystemHealth();
    $healthCheck = new HealthCheck($systemHealth);
    new HttpAuthZ($healthCheck->getHealth())->run();
  }

  if (isset($_SERVER['REQUEST_METHOD'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      getHealthJson();
    }
  }

  if (getenv('TEST') !== false) {

    require_once dirname(__FILE__) . '/../../test/assert.php';
    require_once dirname(__FILE__) . '/../../test/authz.php';
    require_once dirname(__FILE__) . '/../../test/http.php';

    assertEquals(
      'Check health with permission',
      stripMargin(
        '|{
         |    "db": "healthy"
         |}'
      ),
      get(
        'http://localhost:8888/api/v4/health',
        createAccessToken('api:healthcheck:read')
      )
    );

    assertEquals(
      'Fail to check health without permission',
      stripMargin(
        '|{
         |    "cause": "insufficient permissions"
         |}'
      ),
      get(
        'http://localhost:8888/api/v4/health',
        createAccessToken('api:healthczech:read')
      )
    );
  }

?>
