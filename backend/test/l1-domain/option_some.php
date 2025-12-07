<?php

  require_once dirname(__FILE__) . '/../test/assert.php';
  require_once dirname(__FILE__) . '/../../src/l1-domain/option.php';

  $name = 'new Some(6)->getOrElse(7) == 6';
  $expected = 6;
  $observed = new Some(6)->getOrElse(7);

  assertEquals($name, $expected, $observed);

?>
