<?php

  require_once dirname(__FILE__) . '/../test/assert.php';
  require_once dirname(__FILE__) . '/../../src/l3-drivers/db.php';

  $name = 'DB should not work with a bad URL';
  $expected = new None();
  $observed =
    new DB(
      function ($manager) {
        $command = new MongoDB\Driver\Command(['ping' => 1]);
        $cursor = $manager->executeCommand('admin', $command);
        return $cursor->toArray();
      }
    )
    ->map(
      function ($x) {
        return 'yep';
      }
    )
    ->runDB('mongodb://localhost:11111');

  assertEquals($name, $expected, $observed);

?>
