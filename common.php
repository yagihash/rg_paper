<?php
// for debugging
ini_set("error_reporting", E_ALL);
ini_set("display_errors", "1");
ini_set("session.cookie_httponly", 1);

date_default_timezone_set("Asia/Tokyo");

$base_url = dirname($_SERVER["SCRIPT_NAME"]);

$headers = array("Content-Type" => "text/html; charset=UTF-8", "Content-Security-Policy" => "default-src 'self'; style-src 'self' 'unsafe-inline'", "X-XSS-Protection" => "1; mode=block", "X-Content-Type-Options" => "nosniff", "X-Frame-Options" => "DENY");
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

function postParamValidate($param, $mode_array = false) {
  if ($mode_array)
    return isset($_POST[$param]) ? $_POST[$param] : false;
  else
    return (isset($_POST[$param]) and !is_array($_POST[$param])) ? $_POST[$param] : false;
}

function escapeHTML($s) {
  return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}
