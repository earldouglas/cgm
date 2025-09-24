<?php

  require_once dirname(__FILE__) . '/../l1-domain/health.php';


  class SystemHealth implements Health {

    public function isDbHealthy(): bool {

      require_once dirname(__FILE__) . '/db.php';

      return
        new DB(
          function ($manager) {
            return True;
          }
        )
        ->run()
        ->getOrElse(False);
    }
  }

  if (getenv('TEST') !== false) {

    require_once dirname(__FILE__) . '/../test/assert.php';

    $mongoHealth = new SystemHealth();

    $name = 'Health data';
    $expected = True;
    $observed = $mongoHealth->isDbHealthy();

    assertEquals($name, $expected, $observed);
  }

?>
