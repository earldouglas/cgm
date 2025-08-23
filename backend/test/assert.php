<?php

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
