<?php

use Dotenv\Dotenv;

(function() {
  //ts-ignore: true
  Dotenv::createUnsafeImmutable(__DIR__ . '/../')->load();
})();

function resolveIsset(array $arr, string $key) {
  return isset($arr[$key]) && !empty($arr[$key]) ? $arr[$key] : null;
}

function showErrorV2($error) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
	die($error);
}


function clog($args) {
  echo "<pre>";
  echo json_encode([ 'message' => $args ]);
  echo "</pre>";
}
