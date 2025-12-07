<?php

  declare(strict_types=1);

  require_once dirname(__FILE__) . '/../l1-domain/authz.php';
  require_once dirname(__FILE__) . '/../l3-drivers/db.php';

  class DBAuthZ {

    private string $apiSecret;
    private AuthZ $authz;
    private string $dbName;

    function __construct(AuthZ $authz) {
      $this->apiSecret = getenv('API_SECRET');
      $this->authz = $authz;
      $this->dbName = getenv('MONGODB_NAME');
    }

    private function getSubjects($manager, $accessToken) {

      $query = new MongoDB\Driver\Query(array());

      // :: MongoDB\Driver\Cursor
      $cursor = $manager->executeQuery("{$this->dbName}.auth_subjects", $query);

      // Convert cursor to Array and print result
      $allSubjects = $cursor->toArray();

      $matchingSubjects =
        array_filter(
          $allSubjects,
          function ($value) use ($accessToken): bool {

            $subjectId = $value->_id->__toString();
            $subjectName = $value->name;

            $computedAccessToken =
              computeAccessToken($this->apiSecret, $subjectId, $subjectName);

            return $computedAccessToken == $accessToken;
          }
        );

      return $matchingSubjects;
    }

    private function getRoles($manager, $names) {

      $filter = [
        '$or' =>
          array_map(
            fn($name) => array('name' => $name),
            $names
          )
      ];

      $query = new MongoDB\Driver\Query($filter);


      // :: MongoDB\Driver\Cursor
      $cursor = $manager->executeQuery("{$this->dbName}.auth_roles", $query);

      // Convert cursor to Array and print result
      $allRoles = $cursor->toArray();

      return $allRoles;
    }

    private function getPermissions($manager, $accessToken) {

      require_once dirname(__FILE__) . '/../l1-domain/flatmap.php';

      $subjects = $this->getSubjects($manager, $accessToken);

      $permissions =
        array_flatMap(
          function ($subject) use ($manager) {
            return array_flatMap(
              fn($x) => $x->permissions,
              $this->getRoles($manager, $subject->roles)
            );
          },
          $subjects,
        );

      return $permissions;
    }

    function runDBAuthZ($accessToken) {
      $k = function ($manager) use ($accessToken) {
        $permissions = $this->getPermissions($manager, $accessToken);
        $result = $this->authz->runAuthZ($permissions);
        return $result;
      };
      return new DB($k);
    }

  }

?>
