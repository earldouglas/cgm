<?php

  require_once dirname(__FILE__) . '/../test/assert.php';
  require_once dirname(__FILE__) . '/../../src/l1-domain/option.php';

  $name = 'new None()->getOrElse(7) == 7';
  $expected = 7;
  $observed = new None()->getOrElse(7);

  assertEquals($name, $expected, $observed);

?>
