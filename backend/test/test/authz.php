<?php

  require_once dirname(__FILE__) . '/../../src/l1-domain/authz.php';

  function createAccessToken($permissions) {

    $roleName = uniqid();
    $subjectName = uniqId();

    $createRoleResponse =
      post(
        'http://localhost:8888/api/v2/authorization/roles',
        getenv('API_SECRET_SHA1'),
        http_build_query(
          array(
            'name' => $roleName,
            'permissions[]' => $permissions,
            'notes' => '',
          )
        )
      );

    $createSubjectResponse =
      post(
        'http://localhost:8888/api/v2/authorization/subjects',
        getenv('API_SECRET_SHA1'),
        http_build_query(
          array(
            'name' => $subjectName,
            'roles[]' => array($roleName),
            'notes' => '',
          )
        )
      );

    $subject = json_decode($createSubjectResponse)[0];

    $accessToken =
      computeAccessToken(
        getenv('API_SECRET'),
        $subject->_id,
        $subject->name,
      );

    return $accessToken;
  }

?>
