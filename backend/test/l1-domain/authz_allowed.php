<?php

  require_once dirname(__FILE__) . '/../test/assert.php';
  require_once dirname(__FILE__) . '/../../src/l1-domain/authz.php';

  $requiredPermissions = array('api:foo:read');
  $givenPermissions = array('api:foo:read', 'api:foo:write', 'api:bar:read');

  $ran = false;

  $authz =
    new AuthZ(
      $requiredPermissions,
      function () use (&$ran) {
        $ran = true;
        return 42;
      },
    );

  $name = 'AuthZ indicates allowed with sufficient permissions';
  $expected = true;
  $observed = $authz->runAuthZ($givenPermissions)->isAllowed();
  assertEquals($name, $expected, $observed);

  $name = 'AuthZ continuation runs with sufficient permissions';
  $expected = true;
  $observed = $ran;
  assertEquals($name, $expected, $observed);

  $name = 'AuthZ allows access to value with sufficient permissions';
  $expected = 42;
  $observed = $authz->runAuthZ($givenPermissions)->value;
  assertEquals($name, $expected, $observed);

?>
