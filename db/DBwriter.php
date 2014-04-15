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
    if ($this -> link -> connect_error)
      return false;
    if (!$this -> link -> set_charset("utf8"))
      die("Couldn't change encoding to utf8");

    // オートコミットオフ
    // transactQueryにクエリを投げる関数をぶん投げてあげるように
    // $this -> link -> autocommit(false);

    $this -> reader = new DBreader($host, $user, $pass, $db);
  }

  public function __destruct() {
    $this -> link -> close();
  }

  protected function transactQuery(callable $func) {
    try {
      // $this -> link -> begin_transaction();
      $func();
      // $this -> link -> commit();
      return true;
    } catch(Exception $e) {
      // $this -> link -> rollback();
      return $e -> getMessage();
    }
  }

  /**
   * Thanks for http://www.akiyan.com/blog/archives/2011/07/php-mysqli-fetchall.html
   */
  protected function fetchAll(&$stmt) {
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

  protected function isInvalidKeyword($s) {
    $s = str_replace("　", "", $s);
    return $s === "" or ctype_space($s);
  }

  protected function updateData($id, $table, $column, $value) {
    $this -> transactQuery(function() {
      $table = $this -> link -> real_escape_string($table);
      $columns = $this -> link -> real_escape_string($column);
      $stmt = $this -> link -> prepare("UPDATE `$table` SET `$column`=? WHERE `id`=?");
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
   * @param array $user login_name, name_ja, name_en, belongを含む連想配列
   */
  public function addUser($user) {
    return $this -> transactQuery(function() {
      global $user;
      // ユーザ名のチェック → 英数字3-12文字 && 未使用
      if (!isset($user["login_name"]) or $user["login_name"] === false) {
        throw new Exception("Login name is required.");
      } else if (!preg_match("/^[a-zA-Z0-9]{3,12}$/", $user["login_name"])) {
        throw new Exception("Enter a valid login name");
      } else if ($this -> reader -> doesExistsUser($user["login_name"])) {
        $login_name = $user["login_name"];
        $doesExistsUser = true;
      } else {
        $login_name = $user["login_name"];
        $doesExistsUser = false;
      }

      // 氏名(日本語)のチェック
      if (!isset($user["name_ja"]) or $user["name_ja"] === false) {
        $name_ja = "";
      } else if (strlen($user["name_ja"]) > 50) {
        throw new Exception("Name(ja) is too long.");
      } else {
        $name_ja = $user["name_ja"];
      }

      // 氏名(英語)のチェック
      if (!isset($user["name_en"]) or $user["name_en"] === false) {
        $name_en = "";
      } else if (strlen($user["name_en"]) > 50) {
        throw new Exception("Name(en) is too long.");
      } else {
        $name_en = $user["name_en"];
      }

      // 所属のチェック
      if (!isset($user["belong"]) or $user["belong"] === false) {
        throw new Exception("The information about faculty/course which you belong to.");
      } else if (strlen($user["belong"]) > 50) {
        throw new Exception("Your faculty/course name is too long.");
      } else {
        $belong = $user["belong"];
      }

      // 連絡先のチェック
      if (!isset($user["mail"]) or $user["mail"] === false) {
        throw new Exception("Mail address is required.");
      } else if (strlen($user["mail"]) > 256) {
        throw new Exception("Mail address is too long.");
      } else if (!preg_match('/^(?:(?:(?:(?:[a-zA-Z0-9_!#\$\%&\'*+\/=?\^`{}~|\-]+)(?:\.(?:[a-zA-Z0-9_!#\$\%&\'*+\/=?\^`{}~|\-]+))*)|(?:"(?:\\[^\r\n]|[^\\"])*")))\@(?:(?:(?:(?:[a-zA-Z0-9_!#\$\%&\'*+\/=?\^`{}~|\-]+)(?:\.(?:[a-zA-Z0-9_!#\$\%&\'*+\/=?\^`{}~|\-]+))*)|(?:\[(?:\\\S|[\x21-\x5a\x5e-\x7e])*\])))$/', $user["mail"])) {
        throw new Exception("Invalid mail address format.");
      } else {
        $mail = $user["mail"];
      }

      if ($doesExistsUser) {
        $stmt = $this -> link -> prepare("UPDATE `users` SET `name_ja`=?, `name_en`=?, `belong`=?, `mail`=? WHERE `login_name`=?");
        $stmt -> bind_param("sssss", $name_ja, $name_en, $belong, $mail, $login_name);
      } else {
        $stmt = $this -> link -> prepare("INSERT INTO `users` (`login_name`, `name_ja`, `name_en`, `belong`, `mail`) VALUES(?, ?, ?, ?, ?)");
        $stmt -> bind_param("sssss", $login_name, $name_ja, $name_en, $belong, $mail);
      }
      $stmt -> execute();
      if ($stmt -> error !== "") {
        $msg = $stmt -> error;
        $stmt -> close();
        throw new Exception($msg);
      }
      $stmt -> close();
    });
  }

  /**
   * 論文を追加する。
   *
   * @param array $paper user_id, class, title_ja, title_en, file, description_ja, description_en, keywords, mailを含む連想配列
   */
  public function addPaper($paper) {
    return $this -> transactQuery(function() {
      global $paper;
      // ユーザIDのチェック
      if (!isset($paper["user_id"])) {
        throw new Exception("User ID isn't in array.");
      } else {
        $user_id = $paper["user_id"];
      }

      // 論文種別のチェック
      if (!isset($paper["class"]) or $paper["class"] === false) {
        throw new Exception("The class of paper is required. Bachelar/Master/Doctor thesis or Other paper(with pear review or not)");
      } else {
        $class = $paper["class"];
      }

      // タイトル(日本語)のチェック
      if (!isset($paper["title_ja"]) or $paper["title_ja"] === false) {
        $title_ja = "";
      } else if (strlen($paper["title_ja"]) > 256) {
        throw new Exception("The Japanese title is too long.");
      } else {
        $title_ja = $paper["title_ja"];
      }

      // タイトル(英語)のチェック
      if (!isset($paper["title_en"]) or $paper["title_en"] === false) {
        $title_en = "";
      } else if (strlen($paper["title_en"]) > 256) {
        throw new Exception("The English title is too long.");
      } else {
        $title_en = $paper["title_en"];
      }

      // ファイルのチェック
      // TODO: is_pdf, !is_array, もろもろ。受け渡しの方法も。
      $file = $paper["file"];
      $finfo = new finfo(FILEINFO_MIME_TYPE);
      $type = $finfo -> file($file['tmp_name']);
      if (!isset($file['error']) or !is_int($file['error'])) {
        throw new Exception("An error occured in file uploading.");
      } else if (!preg_match("/^application\/pdf/", $type)) {
        throw new Exception("Only pdf file can be accepted.");
      } else if ($file['size'] > 1000000) {
        throw new Exception("Uploaded file is too large.");
      } else {
        if (move_uploaded_file($file["tmp_name"], ($file_path = "./files/" . bin2hex(openssl_random_pseudo_bytes(16)) . ".pdf"))) {
          chmod($file_path, 0644);
          $file_name = basename($file_path);
        } else {
          throw new Exception("An error occured in saving file.");
        }
      }

      // 概要(日本語)のチェック
      if (!isset($paper["description_ja"]) or $paper["description_ja"] === false) {
        $description_ja = "";
      } else if (strlen($paper["description_ja"]) > 2000) {
        throw new Exception("The Japanse description is too long.");
      } else {
        $description_ja = $paper["description_ja"];
      }

      // 概要(英語)のチェック
      if (!isset($paper["description_en"]) or $paper["description_en"] === false) {
        $description_en = "";
      } else if (strlen($paper["description_en"]) > 2000) {
        throw new Exception("The English description is too long");
      } else {
        $description_en = $paper["description_en"];
      }

      // キーワードのチェック
      if (!isset($paper["keywords"]) or $paper["keywords"] === false) {
        throw new Exception("Keywords are required.");
      }
      foreach ($paper["keywords"] as $key => $value) {
        if ($this -> isInvalidKeyword($value))
          unset($paper["keywords"][$key]);
      }
      if (count($paper["keywords"]) < 4) {
        throw new Exception("Four keywords are required at least.");
      } else if (count($paper["keywords"]) > 6) {
        throw new Exception("Too many keywords are posted.");
      } else {
        $keywords = serialize($paper["keywords"]);
      }

      $stmt = $this -> link -> prepare("INSERT INTO `papers` (`user_id`, `class`, `title_ja`, `title_en`, `description_ja`, `description_en`, `keywords`, `file_name`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt -> bind_param("isssssss", $user_id, $class, $title_ja, $title_en, $description_ja, $description_en, $keywords, $file_name);
      $stmt -> execute();
      if ($stmt -> error !== "") {
        unlink($file_path);
        $msg = $stmt -> error;
        $stmt -> close();
        throw new Exception($msg);
      }
      $stmt -> close();
    });
  }

}
