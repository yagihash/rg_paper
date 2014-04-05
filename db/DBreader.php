<?php
/**
 *  DBreader class
 *
 *  This class provides basic methods for reading data from MySQL database.
 *
 *  @access public
 *  @author Yu Yagihashi
 */
class DBreader {
  protected $link;

  public function __construct($host, $user, $pass, $db) {
    $this -> link = new mysqli($host, $user, $pass, $db);
    if ($this -> link -> connect_error) {
      return false;
    }
    $this -> link -> set_charset("utf8");
  }

  public function __destruct() {
    $this -> link -> close();
  }

  /**
   * Thanks for http://www.akiyan.com/blog/archives/2011/07/php-mysqli-fetchall.html
   */
  function fetchAll(&$stmt) {
    $hits = array();
    $params = array();
    $meta = $stmt -> result_metadata();
    while ($field = $meta -> fetch_field()) {
      $params[] = &$row[$field -> name];
    }
    call_user_func_array(array($stmt, 'bind_result'), $params);
    while ($stmt -> fetch()) {
      $c = array();
      foreach ($row as $key => $val) {
        $c[$key] = $val;
      }
      $hits[] = $c;
    }
    return $hits;
  }

  protected function getData($id, $table, $column) {
    $table = $this -> link -> real_escape_string($table);
    $column = $this -> link -> real_escape_string($column);
    $stmt = $this -> link -> prepare("SELECT * FROM $table WHERE $column=?");
    $stmt -> bind_param("i", $id);
    $stmt -> execute();
    $result = $this -> fetch_all($stmt);
    $stmt -> close();
    return $result;
  }

  public function getUser($userid) {
    $result = $this -> get_data($userid, "users", "id");
    return $result;
  }
  
  public function doesExistUser($username) {
    $stmt = $this -> link -> prepare("SELECT id FROM users WHERE username=?");
    $stmt -> bind_param("i", $planid);
    $stmt -> execute();
    $stmt -> bind_result($result);
    $stmt -> fetch();
    $stmt -> close();
    if($result)
      return true;
    else
      return false;
  }

}
