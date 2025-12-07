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

    public function runAuthZ($givenPermissions): AuthZResult {
      if (!array_diff($this->requiredPermissions, $givenPermissions)) {
          // $requiredPermissions is a subset of $givenPermissions
          return new AuthZAllowed(($this->k)());
      } else {
          // $requiredPermissions is NOT a subset of $givenPermissions
          return new AuthZDenied();
      }
    }

  }

?>
