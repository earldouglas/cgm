<?php

  require_once dirname(__FILE__) . '/../test/assert.php';
  require_once dirname(__FILE__) . '/../../src/l1-domain/flatmap.php';

  $xs = [1, 2, 3, 4];

  $name = 'array_flatMap';
  $expected = [1, 2, 2, 4, 3, 6, 4, 8];
  $observed = array_flatMap(
    fn($x) => array($x, $x * 2),
    $xs,
  );

  assertEquals($name, $expected, $observed);

?>
