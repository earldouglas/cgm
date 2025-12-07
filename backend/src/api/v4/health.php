<?php

  function getHealthJson() {

    require_once dirname(__FILE__) . '/../../l1-domain/authz.php';
    require_once dirname(__FILE__) . '/../../l1-domain/health.php';
    require_once dirname(__FILE__) . '/../../l2-use-cases/healthcheck.php';
    require_once dirname(__FILE__) . '/../../l3-drivers/health.php';
    require_once dirname(__FILE__) . '/../../l4-http/authz.php';

    $dbUrl = getenv('MONGODB_ROOT') . "/" . getenv('MONGODB_NAME');
    $systemHealth = new SystemHealth($dbUrl);
    $healthCheck = new HealthCheck($systemHealth);
    new HttpAuthZ($dbUrl, $healthCheck->getHealth())->runHttpAuthZ();
  }

  if (isset($_SERVER['REQUEST_METHOD'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      getHealthJson();
    }
  }

?>
