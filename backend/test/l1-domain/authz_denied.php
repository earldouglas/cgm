<?php

  require_once dirname(__FILE__) . '/../test/assert.php';
  require_once dirname(__FILE__) . '/../../src/l1-domain/authz.php';

  $requiredPermissions = array('api:foo:read');
  $givenPermissions = array('api:foo:write', 'api:bar:read');

  $ran = false;

  $authz =
    new AuthZ(
      $requiredPermissions,
      function () use (&$ran) {
        $ran = true;
        return 42;
      },
    );

  $name = 'AuthZ indicates denied with insufficient permissions';
  $expected = false;
  $observed = $authz->runAuthZ($givenPermissions)->isAllowed();
  assertEquals($name, $expected, $observed);

  $name = 'AuthZ continuation does not run with insufficient permissions';
  $expected = false;
  $observed = $ran;
  assertEquals($name, $expected, $observed);

?>
