<?php

  declare(strict_types=1);

  require_once dirname(__FILE__) . '/../l1-domain/authz.php';
  require_once dirname(__FILE__) . '/../l3-drivers/authz.php';

  class HttpAuthZ {

    private $authz;

    function __construct(AuthZ $authz) {
      $this->authz = new MongoAuthZ($authz);
    }

    function run() {
      $accessToken = $_SERVER['HTTP_API_SECRET'];
      $result = $this->authz->run($accessToken);

      if ($result->isAllowed()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(
          $result->value,
          JSON_PRETTY_PRINT,
        );
      } else {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(
          array('cause' => 'insufficient permissions'),
          JSON_PRETTY_PRINT,
        );
      }
    }

  }

?>
