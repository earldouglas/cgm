<?php

  require_once dirname(__FILE__) . '/../l2-use-cases/healthcheck.php';

  class MongoDBHealth implements DBHealth {

    public function isHealthy(): bool {

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

    $mongoDBHealth = new MongoDBHealth();

    $name = 'Health data';
    $expected = True;
    $observed = $mongoDBHealth->isHealthy();

    assertEquals($name, $expected, $observed);
  }

?>
