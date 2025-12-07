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

?>
