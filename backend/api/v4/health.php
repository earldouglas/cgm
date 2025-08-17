<?php

  require_once dirname(__FILE__) . '/../../lib/db.php';

  $db =
    new DB(
      function ($manager) {
        return 'ok';
      }
    )->orElse(
      function () {
        return 'nope';
      }
    );

  echo
    json_encode(
      [
        'db' => $db,
      ],
      JSON_PRETTY_PRINT
    );

?>
