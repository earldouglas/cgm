<?php

  declare(strict_types=1);

  require_once dirname(__FILE__) . '/../l1-domain/authz.php';

  class MongoAuthZ {

    private $apiSecret;
    private $authz;

    function __construct(AuthZ $authz) {
      $this->apiSecret = getenv('API_SECRET');
      $this->authz = $authz;
    }

    private function getSubjects($accessToken) {

      $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
      $query = new MongoDB\Driver\Query(array());

      // :: MongoDB\Driver\Cursor
      $cursor = $manager->executeQuery('cgm.auth_subjects', $query);

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

    private function getRoles($names) {

      $manager =
        new MongoDB\Driver\Manager("mongodb://localhost:27017");

      $filter = [
        '$or' =>
          array_map(
            fn($name) => array('name' => $name),
            $names
          )
      ];

      $query = new MongoDB\Driver\Query($filter);


      // :: MongoDB\Driver\Cursor
      $cursor = $manager->executeQuery('cgm.auth_roles', $query);

      // Convert cursor to Array and print result
      $allRoles = $cursor->toArray();

      return $allRoles;
    }

    private function getPermissions($accessToken) {

      require_once dirname(__FILE__) . '/../l1-domain/flatmap.php';

      $subjects = $this->getSubjects($accessToken);

      $permissions =
        array_flatMap(
          function ($subject) {
            return array_flatMap(
              fn($x) => $x->permissions,
              $this->getRoles($subject->roles)
            );
          },
          $subjects,
        );

      return $permissions;
    }

    function run($accessToken) {
      $permissions = $this->getPermissions($accessToken);
      $result = $this->authz->run($permissions);
      return $result;
    }

  }

?>
