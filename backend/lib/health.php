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

    return
      json_encode(
        [
          'db' => $db,
        ],
        JSON_PRETTY_PRINT
      );
  }

  if (getenv('TEST') !== false) {

    require_once dirname(__FILE__) . '/../test/assert.php';

    assertEquals(
      'Health check shows db: ok',
      'ok',
      json_decode(getHealth(), true)['db'],
    );
  }

?>
