<?php
require_once (__DIR__ . "/DBreader.php");
require_once (__DIR__ . "/DBwriter.php");

class DBinterface {
  private $host, $user, $pass, $db;
  public $reader, $writer;

  public function __construct() {
    $this -> host = "localhost";
    $this -> user = "user";
    $this -> pass = "password";
    $this -> db = "rg_paper";
    $this -> reader = new DBreader();
    $this -> writer = new DBwriter();
  }
  
}
