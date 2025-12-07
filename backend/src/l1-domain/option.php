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

?>
