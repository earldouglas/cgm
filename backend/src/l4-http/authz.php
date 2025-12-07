<?php

  declare(strict_types=1);

  require_once dirname(__FILE__) . '/../l1-domain/authz.php';
  require_once dirname(__FILE__) . '/../l3-drivers/authz.php';

  class HttpAuthZ {

    private string $dbUrl;
    private DBAuthZ $dbAuthz;

    function __construct(string $dbUrl, AuthZ $authz) {
      $this->dbUrl = $dbUrl;
      $this->dbAuthz = new DBAuthZ($authz);
    }

    function runHttpAuthZ() {
      $accessToken = $_SERVER['HTTP_API_SECRET'];
      $result =
        $this
          ->dbAuthz
          ->runDBAuthZ($accessToken)
          ->runDB($this->dbUrl)
          ->getOrElse(new AuthZDenied());

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
