<?php

  class DB {

    private $uri;
    private $k;

    function __construct(callable $k, string $uri = 'mongodb://localhost:27017') {
      $this->k = $k;
      $this->uri = $uri;
    }

    public function orElse($k) {

      $result = NULL;

      try {
        $manager = new MongoDB\Driver\Manager($this->uri);
        $command = new MongoDB\Driver\Command(['ping' => 1]);
        $cursor = $manager->executeCommand('admin', $command);
        $result = ($this->k)($manager);
      } catch(MongoDB\Driver\Exception\Exception $e) {
        $message = "connection failure: $e";
        error_log($message);
        $result = $k();
      }

      return $result;
    }

    public function map(callable $f) {
      $k = function ($manager) use ($f) {
        $x = ($this->k)($manager);
        return $f($x);
      };
      return new DB($k, $this->uri);
    }

    public function flatMap(callable $f) {
      $k = function () use ($f) {
        $x = ($this->k)($manager);
        return ($f($x)->k)($manager);
      };
      return new DB($k, $this->uri);
    }
  }

  if (getenv('TEST') !== false) {

    require_once dirname(__FILE__) . '/../test/assert.php';

    assertEquals(
      'DB should work with default URI',
      'yep',
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
      )->orElse(
        function () {
          return 'nope';
        }
      )
    );

    assertEquals(
      'DB should not work with a bad URI',
      'nope',
      new DB(
        function ($manager) {
          $command = new MongoDB\Driver\Command(['ping' => 1]);
          $cursor = $manager->executeCommand('admin', $command);
          return $cursor->toArray();
        },
        'mongodb://localhost:11111'
      )->map(
        function ($x) {
          return 'yep';
        }
      )->orElse(
        function () {
          return 'nope';
        }
      )
    );
  }

?>
