<?php

  interface DBHealth {
    public function isHealthy(): bool;
  }

  class HealthCheck {

    public final DBHealth $dbHealth;

    public function __construct(DBHealth $dbHealth) {
      $this->dbHealth = $dbHealth;
    }

    public function getHealth() {
      return [
        'db' => $this->dbHealth->isHealthy() ? 'healthy' : 'unhealthy',
      ];
    }
  }

  if (getenv('TEST') !== false) {

    require_once dirname(__FILE__) . '/../test/assert.php';

    class HealthyDB implements DBHealth {
      public function isHealthy(): bool {
        return True;
      }
    }

    $healthyDb = new HealthyDB();
    $healthCheck = new HealthCheck($healthyDb);
    $dbHealth = $healthCheck->dbHealth->isHealthy();

    assertEquals(
      $name = 'healthy DB is healthy',
      $expected = True,
      $observed = $dbHealth,
    );

  }

  if (getenv('TEST') !== false) {

    require_once dirname(__FILE__) . '/../test/assert.php';

    class UnhealthyDB implements DBHealth {
      public function isHealthy(): bool {
        return False;
      }
    }

    $unhealthyDb = new UnhealthyDB();
    $healthCheck = new HealthCheck($unhealthyDb);
    $dbHealth = $healthCheck->dbHealth->isHealthy();

    assertEquals(
      $name = 'unhealthy DB is not healthy',
      $expected = False,
      $observed = $dbHealth,
    );

  }

?>
