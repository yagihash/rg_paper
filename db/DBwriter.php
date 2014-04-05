<?php
require_once (__DIR__ . "/DBreader.php");

/**
 *  DBwriter class
 *
 *  This class provides basic methods for writing data into MySQL database.
 *
 *  @access public
 *  @author Yu Yagihashi
 */
class DBwriter {
  protected $link;
  protected $reader;

  public function __construct($host, $user, $pass, $db) {
    $this -> link = new mysqli($host, $user, $pass, $db);
    if ($this -> link -> connect_error) {
      return false;
    }
    $this -> link -> set_charset("utf8");

    // オートコミットオフ
    // transactQueryにクエリを投げる関数をぶん投げてあげるように
    $this -> link -> autocommit(false);

    $this -> reader = new DBreader($host, $user, $pass, $db);
  }

  public function __destruct() {
    $this -> link -> close();
  }

  protected function transactQuery(callable $func) {
    try {
      $this -> link -> begin_transaction();
      $func();
      $this -> link -> commit();
    } catch(Exception $e) {
      $this -> link -> rollback();
      return $e -> getMessage();
    }
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
    call_user_func_array(array($stmt, "bind_result"), $params);
    while ($stmt -> fetch()) {
      $c = array();
      foreach ($row as $key => $val) {
        $c[$key] = $val;
      }
      $hits[] = $c;
    }
    return $hits;
  }

  protected function updateData($id, $table, $column, $value) {
    $this -> transactQuery(function() {
      $table = $this -> link -> real_escape_string($table);
      $columns = $this -> link -> real_escape_string($column);
      $stmt = $this -> link -> prepare("UPDATE $table SET $column=? WHERE id=?");
      $stmt -> bind_param("si", $value, $id);
      $stmt -> execute();
      $stmt -> close();
      if ($stmt -> error !== "")
        throw new Exception("Failed to update data.");
    });
  }

  /**
   * ユーザを追加する。
   *
   * @param array $user username, passwordを含む連想配列
   */
  public function addUser($user) {
    $this -> transactQuery(function() {
      // ユーザ名のチェック → 英数字アンダースコア3-12文字 && 未使用
      if (!isset($user["username"])) {
        throw new Exception("ユーザ名は必須項目です。");
      } else if (!preg_match("/^[a-zA-Z0-9_]{3,12}$/", $user["username"])) {
        throw new Exception("英数字アンダースコアで構成される3字以上12文字以下のユーザ名を指定してください。");
      } else if ($this -> reader -> does_exist_user($user["username"])) {
        throw new Exception("このユーザ名は既に使用されています。");
      } else {
        $username = $user["username"];
      }
      /** /
       // スクリーンネームのチェック → 1-30文字 && 制御文字はストリップ
       if (!isset($user["screenname"])) {
       throw new Exception("スクリーンネームは必須項目です。");
       } else if (!preg_match("/.+{1,30}/", $user["screenname"])) {
       throw new Exception("スクリーンネームは30字までです。");
       } else {
       $screenname = preg_replace("/[\x00-\x1f\x7f]/", "", $user["screenname"]);
       }

       // 性別のチェック → 0: 非公開, 1: 男性, 2: 女性
       if (!isset($user["gender"])) {
       throw new Exception("性別は必須項目です。");
       } else if (is_numeric($user["gender"]) and $user["gender"] >= 0 and $user["gender"] < 3) {
       $gender = $user["gender"];
       } else {
       throw new Exception("正しい性別を入力してください。");
       }

       // メールアドレスのチェック → 適当に…
       // cited from http://hello.lumiere-couleur.com/smilkobuta/2010/12/03/%E3%80%8Cphp%E4%BD%BF%E3%81%84%E3%81%AF%E3%82%82%E3%81%86%E6%AD%A3%E8%A6%8F%E8%A1%A8%E7%8F%BE%E3%82%92blog%E3%81%AB%E6%9B%B8%E3%81%8F%E3%81%AA%E3%80%8D%E3%81%AE%E3%83%A1%E3%83%BC%E3%83%AB%E3%82%A2/
       if (!isset($user["email"])) {
       throw new Exception("メールアドレスは必須項目です。");
       } elseif (!preg_match('/^(?:(?:(?:(?:[a-zA-Z0-9_!#\$\%&\'*+\/=?\^`{}~|\-]+)(?:\.(?:[a-zA-Z0-9_!#\$\%&\'*+\/=?\^`{}~|\-]+))*)|(?:"(?:\\[^\r\n]|[^\\"])*")))\@(?:(?:(?:(?:[a-zA-Z0-9_!#\$\%&\'*+\/=?\^`{}~|\-]+)(?:\.(?:[a-zA-Z0-9_!#\$\%&\'*+\/=?\^`{}~|\-]+))*)|(?:\[(?:\\\S|[\x21-\x5a\x5e-\x7e])*\])))$/', $email)) {
       throw new Exception("正しいメールアドレスを入力してください。");
       } else {
       $email = $user["email"];
       }
       /**/
       
      // パスワードのチェック → 英数字記号のみ8文字以上
      if (!isset($user["password"])) {
        throw new Exception("パスワードは必須項目です。");
      } else if (!preg_match("/[\@-\~]/", $user["password"])) {
        throw new Exception("パスワードに使用できない文字が指定されています。英数字および記号のみを使用してください。");
      } else if (strlen($user["password"] < 8)) {
        throw new Exception("パスワードは8文字以上のものを入力してください。");
      } else {
        $salt = bin2hex(openssl_random_pseudo_bytes(40));
        $password = hash("sha256", $salt . $user["password"]);
      }
      $stmt = $this -> link -> prepare("INSERT INTO users(username, screenname, gender, email, password, salt)");
      $stmt -> bind_param("ssssss", $username, $screenname, $gender, $email, $password, $salt);
      $stmt -> execute();
      $stmt -> close();
    });
  }

}
