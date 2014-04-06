<?php
require_once (__DIR__ . "/common.php");
require_once (__DIR__ . "/db/DBinterface.php");

$db_if = new DBinterface();
$reader = $db_if -> reader;
$writer = $db_if -> writer;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("HTTP/1.1 403 Forbidden");
  die("403 Forbidden");
}

$user = array("login_name" => postParamValidate("login_name"), "name_ja" => postParamValidate("name_ja"), "name_en" => postParamValidate("name_en"), "belong" => postParamValidate("belong"), "mail" => postParamValidate("mail"));
$addUser = $writer -> addUser($user);
if ($addUser !== true)
  die($addUser);

$user_id = $reader -> getUserId("yagihash");
$user_id = $user_id[0]["id"];
$paper = array("user_id" => $user_id, "class" => postParamValidate("class"), "title_ja" => postParamValidate("title_ja"), "title_en" => postParamValidate("title_en"), "description_ja" => postParamValidate("description_ja"), "description_en" => postParamValidate("description_en"), "keywords" => postParamValidate("keywords", 1), "file" => isset($_FILES["file"]) ? $_FILES["file"] : false);

$addPaper = $writer -> addPaper($paper);
if ($addPaper !== true)
  die($addPaper);

header("Location: {$base_url}");
