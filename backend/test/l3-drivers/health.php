<?php

  require_once dirname(__FILE__) . '/../test/assert.php';
  require_once dirname(__FILE__) . '/../../src/l3-drivers/health.php';

  $mongoHealth = new SystemHealth('mongodb://localhost:27017');

  $name = 'Health data';
  $expected = True;
  $observed = $mongoHealth->isDbHealthy();

  assertEquals($name, $expected, $observed);

?>
