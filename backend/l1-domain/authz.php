<?php

  function computeAccessToken($apiSecret, $subjectId, $subjectName) {
    $abbrev = substr(preg_replace('/[\W]/', '', $subjectName), 0, 10);
    $hash = substr(sha1(sha1($apiSecret) . $subjectId), 0, 16);
    return $abbrev . '-' . $hash;
  }

  interface AuthZResult {
    public function isAllowed(): bool;
  }

  class AuthZAllowed implements AuthZResult {

    public $value;

    function __construct($value) {
      $this->value = $value;
    }

    public function __toString(): string {
      return "AuthZAllowed($this->value)";
    }

    public function isAllowed(): bool {
      return true;
    }
  }

  class AuthZDenied implements AuthZResult {

    public function __toString(): string {
      return 'AuthZDenied';
    }

    public function isAllowed(): bool {
      return false;
    }
  }

  class AuthZ {

    private $requiredPermissions;
    private $k;

    function __construct($requiredPermissions, $k) {
      $this->requiredPermissions = $requiredPermissions;
      $this->k = $k;
    }

    public function run($givenPermissions): AuthZResult {
      if (!array_diff($this->requiredPermissions, $givenPermissions)) {
          // $requiredPermissions is a subset of $givenPermissions
          return new AuthZAllowed(($this->k)());
      } else {
          // $requiredPermissions is NOT a subset of $givenPermissions
          return new AuthZDenied();
      }
    }

  }

  if (getenv('TEST') !== false) {

    require_once dirname(__FILE__) . '/../test/assert.php';

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
    $observed = $authz->run($givenPermissions)->isAllowed();
    assertEquals($name, $expected, $observed);

    $name = 'AuthZ continuation runs with sufficient permissions';
    $expected = true;
    $observed = $ran;
    assertEquals($name, $expected, $observed);

    $name = 'AuthZ allows access to value with sufficient permissions';
    $expected = 42;
    $observed = $authz->run($givenPermissions)->value;
    assertEquals($name, $expected, $observed);
  }

  if (getenv('TEST') !== false) {

    require_once dirname(__FILE__) . '/../test/assert.php';

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
    $observed = $authz->run($givenPermissions)->isAllowed();
    assertEquals($name, $expected, $observed);

    $name = 'AuthZ continuation does not run with insufficient permissions';
    $expected = false;
    $observed = $ran;
    assertEquals($name, $expected, $observed);
  }

?>
