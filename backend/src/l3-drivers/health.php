<?php

  require_once dirname(__FILE__) . '/../l1-domain/health.php';


  class SystemHealth implements Health {

    private string $dbUrl;

    function __construct(string $dbUrl) {
      $this->dbUrl = $dbUrl;
    }

    public function isDbHealthy(): bool {

      require_once dirname(__FILE__) . '/db.php';

      return
        new DB(
          function ($manager) {
            return True;
          }
        )
          ->runDB($this->dbUrl)
          ->getOrElse(False);
    }
  }

?>
