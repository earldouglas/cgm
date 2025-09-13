<?php

  function stripMargin($x) {
    $lines = explode("\n", $x);

    $strippedLines =
      array_map(
        function ($line) {
          return preg_replace('/^\s*[|]/', '', $line);
        },
        $lines
      );

    $stripped = join("\n", $strippedLines);

    return $stripped;
  }

  function assertEquals($name, $expected, $observed) {
    if ($expected == $observed) {
      echo "✔ $name\n";
    } else {
      echo "✖ $name\n";
      echo "  expected: $expected\n";
      echo "  observed: $observed\n";
      exit(1);
    }
  }

?>
