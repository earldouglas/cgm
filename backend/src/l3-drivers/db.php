<?php

  require_once dirname(__FILE__) . '/../l1-domain/option.php';

  class DB {

    private $k;

    function __construct(callable $k) {
      $this->k = $k;
    }

    public function runDB(string $uri) {

      $result = new None();

      try {
        $manager = new MongoDB\Driver\Manager($uri);
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

?>
