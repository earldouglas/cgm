<?php

  function getHealthJson() {

    require_once dirname(__FILE__) . '/../../lib/health.php';

    $health = getHealth();

    $json =
      json_encode(
        $health,
        JSON_PRETTY_PRINT
      );

    return $json;
  }

  if (isset($_SERVER['REQUEST_METHOD'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      echo getHealthJson();
    }
  }

?>
