<?php
require_once (__DIR__ . "/common.php");

$file_name = isset($_GET["f"]) ? $_GET["f"] : "";
if(preg_match("/[^a-f0-9]/", substr($file_name, 0, -4))) {
  echo substr($file_name, 0, -4);
  die("403 Forbidden");
}
$file_name = basename($file_name);

if (preg_match("/\A[a-z0-9]{32}\.pdf\z/", $file_name)) {
  $file_path = "./files/" . $file_name;
  if (preg_match("/\A\.\/files\/[a-f0-9]{32}\.pdf\z/", $file_path) and file_exists($file_path)) {
    header("Content-Type: application/pdf");
    header('Content-Disposition: inline; filename="' . basename($file_name) . '"');
    readfile($file_path);
  } else {
    header("HTTP/1.1 403 Forbidden");
    die("403 Forbidden");
  }
} else {
  header("HTTP/1.1 403 Forbidden");
  die("403 Forbidden");
}
