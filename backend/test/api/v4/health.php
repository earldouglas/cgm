<?php

  require_once dirname(__FILE__) . '/../../test/assert.php';
  require_once dirname(__FILE__) . '/../../test/http.php';
  require_once dirname(__FILE__) . '/../../test/authz.php';

  require_once dirname(__FILE__) . '/../../../src/api/v4/health.php';


  assertEquals(
    'Check health with permission',
    stripMargin(
      '|{
       |    "db": "healthy"
       |}'
    ),
    get(
      'http://localhost:8888/api/v4/health',
      createAccessToken('api:healthcheck:read')
    )
  );

  assertEquals(
    'Fail to check health without permission',
    stripMargin(
      '|{
       |    "cause": "insufficient permissions"
       |}'
    ),
    get(
      'http://localhost:8888/api/v4/health',
      createAccessToken('api:healthczech:read')
    )
  );

?>
