<?php

  (function () {

    require_once dirname(__FILE__) . '/../test/assert.php';
    require_once dirname(__FILE__) . '/../../src/l2-use-cases/healthcheck.php';

    $healthyDb =
      new class implements Health {
        public function isDbHealthy(): bool {
          return True;
        }
      };

    $healthCheck = new HealthCheck($healthyDb);

    $getHealthAuthZ = $healthCheck->getHealth();
    $getHealthResult = $getHealthAuthZ->runAuthZ(array('api:healthcheck:read'));

    assertEquals(
      $name = 'get health with correct permissions',
      $expected = True,
      $observed = $getHealthResult->isAllowed(),
    );

  })();

  (function () {

    require_once dirname(__FILE__) . '/../test/assert.php';
    require_once dirname(__FILE__) . '/../../src/l2-use-cases/healthcheck.php';

    $healthyDb =
      new class implements Health {
        public function isDbHealthy(): bool {
          return True;
        }
      };

    $healthCheck = new HealthCheck($healthyDb);

    $getHealthAuthZ = $healthCheck->getHealth();
    $getHealthResult = $getHealthAuthZ->runAuthZ(array('api:foo:read'));

    assertEquals(
      $name = 'fail to get health with incorrect permissions',
      $expected = False,
      $observed = $getHealthResult->isAllowed(),
    );

  })();

  (function () {

    require_once dirname(__FILE__) . '/../test/assert.php';
    require_once dirname(__FILE__) . '/../../src/l2-use-cases/healthcheck.php';

    $healthyDb =
      new class implements Health {
        public function isDbHealthy(): bool {
          return True;
        }
      };

    $healthCheck = new HealthCheck($healthyDb);

    $getHealthAuthZ = $healthCheck->getHealth();
    $getHealthResult = $getHealthAuthZ->runAuthZ(array('api:healthcheck:read'));

    assertEquals(
      $name = 'healthy db is healthy',
      $expected = 'healthy',
      $observed = $getHealthResult->value['db'],
    );

  })();

  (function () {

    require_once dirname(__FILE__) . '/../test/assert.php';
    require_once dirname(__FILE__) . '/../../src/l2-use-cases/healthcheck.php';

    $unhealthyDb =
      new class implements Health {
        public function isDbHealthy(): bool {
          return False;
        }
      };

    $healthCheck = new HealthCheck($unhealthyDb);

    $getHealthAuthZ = $healthCheck->getHealth();
    $getHealthResult = $getHealthAuthZ->runAuthZ(array('api:healthcheck:read'));

    assertEquals(
      $name = 'unhealthy db is unhealthy',
      $expected = 'unhealthy',
      $observed = $getHealthResult->value['db'],
    );

  })();

?>
