<?php

  require_once dirname(__FILE__) . '/option.php';

  class DB {

    private $k;

    function __construct(callable $k) {
      $this->k = $k;
    }

    public function run(string $uri = 'mongodb://localhost:27017') {

      $result = new None();

      try {
        $manager = new MongoDB\Driver\Manager($uri);
        $command = new MongoDB\Driver\Command(['ping' => 1]);
        $cursor = $manager->executeCommand('admin', $command);
        $result = new Some(($this->k)($manager));
      } catch(MongoDB\Driver\Exception\Exception $e) {
        $message = "connection failure: $e";
        error_log($message);
      }

      return $result;
    }

    public function map(callable $f) {
      $k = function ($manager) use ($f) {
        $x = ($this->k)($manager);
        return $f($x);
      };
      return new DB($k);
    }

    public function flatMap(callable $f) {
      $k = function () use ($f) {
        $x = ($this->k)($manager);
        return ($f($x)->k)($manager);
      };
      return new DB($k);
    }
  }

  if (getenv('TEST') !== false) {

    require_once dirname(__FILE__) . '/../test/assert.php';

    $name = 'DB should work with default URI';
    $expected = new Some('yep');
    $observed =
      new DB(
        function ($manager) {
          $command = new MongoDB\Driver\Command(['ping' => 1]);
          $cursor = $manager->executeCommand('admin', $command);
          return $cursor->toArray();
        }
      )->map(
        function ($x) {
          return 'yep';
        }
      )
      ->run();

    assertEquals($name, $expected, $observed);
  }

  if (getenv('TEST') !== false) {

    require_once dirname(__FILE__) . '/../test/assert.php';

    $name = 'DB should not work with a bad URI';
    $expected = new None();
    $observed =
      new DB(
        function ($manager) {
          $command = new MongoDB\Driver\Command(['ping' => 1]);
          $cursor = $manager->executeCommand('admin', $command);
          return $cursor->toArray();
        }
      )->map(
        function ($x) {
          return 'yep';
        }
      )
      ->run('mongodb://localhost:11111');

    assertEquals($name, $expected, $observed);
  }

?>
