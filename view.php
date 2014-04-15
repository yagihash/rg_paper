<?php
require_once(__DIR__ . "/common.php");
require_once (__DIR__ . "/db/DBinterface.php");

$DBinterface = new DBinterface();
$reader = $DBinterface -> reader;
$result = $reader -> getPapers();
var_dump($result);
