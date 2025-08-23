<?php

  interface Option {

    public function getOrElse($x);

  }

  class Some implements Option {

    private $x;

    function __construct($x) {
      $this->x = $x;
    }

    public function getOrElse($y) {
      return $this->x;
    }

    public function __toString(): string {
      return "Some($this->x)";
    }

  }

  class None implements Option {

    public function getOrElse($y) {
      return $y;
    }

    public function __toString(): string {
      return "None";
    }

  }

  if (getenv('TEST') !== false) {

    require_once dirname(__FILE__) . '/../test/assert.php';

    $name = 'new Some(6)->getOrElse(7) == 6';
    $expected = 6;
    $observed = new Some(6)->getOrElse(7);

    assertEquals($name, $expected, $observed);
  }

  if (getenv('TEST') !== false) {

    require_once dirname(__FILE__) . '/../test/assert.php';

    $name = 'new None()->getOrElse(7) == 7';
    $expected = 7;
    $observed = new None()->getOrElse(7);

    assertEquals($name, $expected, $observed);
  }

?>
