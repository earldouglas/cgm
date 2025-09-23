<?php

  require_once dirname(__FILE__) . '/../l1-domain/health.php';

  class HealthCheck {

    public final Health $health;

    public function __construct(Health $health) {
      $this->health = $health;
    }

    public function getHealth() {
      return [
        'db' => $this->health->isDbHealthy() ? 'healthy' : 'unhealthy',
      ];
    }
  }

  if (getenv('TEST') !== false) {

    require_once dirname(__FILE__) . '/../test/assert.php';

    class HealthyDB implements Health {
      public function isDbHealthy(): bool {
        return True;
      }
    }

    $healthyDb = new HealthyDB();
    $healthCheck = new HealthCheck($healthyDb);
    $health = $healthCheck->health->isDbHealthy();

    assertEquals(
      $name = 'healthy DB is healthy',
      $expected = True,
      $observed = $health,
    );

  }

  if (getenv('TEST') !== false) {

    require_once dirname(__FILE__) . '/../test/assert.php';

    class UnhealthyDB implements Health {
      public function isDbHealthy(): bool {
        return False;
      }
    }

    $unhealthyDb = new UnhealthyDB();
    $healthCheck = new HealthCheck($unhealthyDb);
    $health = $healthCheck->health->isDbHealthy();

    assertEquals(
      $name = 'unhealthy DB is not healthy',
      $expected = False,
      $observed = $health,
    );

  }

?>
