<?php

  function array_flatMap($f, $xs) {
    $result = array();

    array_walk(
      $xs,
      function ($x) use ($f, &$result) {
        $ys = $f($x);
        $result = array_merge($result, $ys);
      }
    );

    return $result;
  }

?>
