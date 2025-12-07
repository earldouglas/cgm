<?php

  require_once dirname(__FILE__) . '/../test/assert.php';
  require_once dirname(__FILE__) . '/../../src/l3-drivers/db.php';

  $dbUrl = getenv('MONGODB_ROOT') . "/" . getenv('MONGODB_NAME');

  $name = 'DB should work with default URL';
  $expected = new Some('yep');
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
    ->runDB($dbUrl);

  assertEquals($name, $expected, $observed);

?>
