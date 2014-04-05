<?php
date_default_timezone_set("Asia/Tokyo");

$headers = array("Content-Type" => "text/html; charset=UTF-8", "Content-Security-Policy" => "default-src 'self'; style-src 'self' 'unsafe-inline'", "X-XSS-Protection" => "1; mode=block", "X-Content-Type-Options" => "nosniff", "X-Frame-Options" => "deny");
foreach ($headers as $key => $val)
  header("{$key}: {$val}");

function issueToken() {
  if (!isset($_SESSION["token"])) {
    $token = bin2hex(openssl_random_pseudo_bytes(32));
    $_SESSION["token"] = $token;
  }
  return $_SESSION["token"];
}

function checkToken($token) {
  return $_SESSION["token"] === $token;
}

function postParamValidate($param) {
  return (isset($_POST[$param]) and !is_array($_POST[$param])) ? $_POST[$param] : false;
}

function escapeHTML($s) {
  return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}
