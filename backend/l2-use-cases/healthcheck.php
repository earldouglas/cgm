<?php

  require_once dirname(__FILE__) . '/../l1-domain/authz.php';
  require_once dirname(__FILE__) . '/../l1-domain/health.php';

  class HealthCheck {

    private Health $health;

    public function __construct(Health $health) {
      $this->health = $health;
    }

    public function getHealth() {

      $requiredPermissions = array('api:healthcheck:read');
      $health = $this->health;
      $k =
        function () use ($health) {
          $isDbHealthy = $health->isDbHealthy();
          return [
            'db' => $isDbHealthy ? 'healthy' : 'unhealthy',
          ];
        };

      return
        new AuthZ(
          $requiredPermissions,
          $k,
        );
    }
  }

  if (getenv('TEST') !== false) {

    (function () {

      require_once dirname(__FILE__) . '/../test/assert.php';

      $healthyDb =
        new class implements Health {
          public function isDbHealthy(): bool {
            return True;
          }
        };

      $healthCheck = new HealthCheck($healthyDb);

      $getHealthAuthZ = $healthCheck->getHealth();
      $getHealthResult = $getHealthAuthZ->run(array('api:healthcheck:read'));

      assertEquals(
        $name = 'get health with correct permissions',
        $expected = True,
        $observed = $getHealthResult->isAllowed(),
      );

    })();

    (function () {

      require_once dirname(__FILE__) . '/../test/assert.php';

      $healthyDb =
        new class implements Health {
          public function isDbHealthy(): bool {
            return True;
          }
        };

      $healthCheck = new HealthCheck($healthyDb);

      $getHealthAuthZ = $healthCheck->getHealth();
      $getHealthResult = $getHealthAuthZ->run(array('api:foo:read'));

      assertEquals(
        $name = 'fail to get health with incorrect permissions',
        $expected = False,
        $observed = $getHealthResult->isAllowed(),
      );

    })();

    (function () {

      require_once dirname(__FILE__) . '/../test/assert.php';

      $healthyDb =
        new class implements Health {
          public function isDbHealthy(): bool {
            return True;
          }
        };

      $healthCheck = new HealthCheck($healthyDb);

      $getHealthAuthZ = $healthCheck->getHealth();
      $getHealthResult = $getHealthAuthZ->run(array('api:healthcheck:read'));

      assertEquals(
        $name = 'healthy db is healthy',
        $expected = 'healthy',
        $observed = $getHealthResult->value['db'],
      );

    })();

    (function () {

      require_once dirname(__FILE__) . '/../test/assert.php';

      $unhealthyDb =
        new class implements Health {
          public function isDbHealthy(): bool {
            return False;
          }
        };

      $healthCheck = new HealthCheck($unhealthyDb);

      $getHealthAuthZ = $healthCheck->getHealth();
      $getHealthResult = $getHealthAuthZ->run(array('api:healthcheck:read'));

      assertEquals(
        $name = 'unhealthy db is unhealthy',
        $expected = 'unhealthy',
        $observed = $getHealthResult->value['db'],
      );

    })();
  }

?>
