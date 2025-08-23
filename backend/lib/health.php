<?php

  function getHealth() {

    require_once dirname(__FILE__) . '/db.php';

    $db =
      new DB(
        function ($manager) {
          return 'ok';
        }
      )
      ->run()
      ->getOrElse('nope');

    return [
      'db' => $db,
    ];
  }

  if (getenv('TEST') !== false) {

    require_once dirname(__FILE__) . '/../test/assert.php';

    $name = 'Health data';
    $expected = [ 'db' => 'ok' ];
    $observed = getHealth();

    assertEquals($name, $expected, $observed);
  }

?>
