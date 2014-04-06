<?php
require_once (__DIR__ . "/common.php");
?>
<!DOCTYPE html>

<html lang="ja">
  <head>
    <meta charset="UTF-8" />
    <title>Uploader</title>
    <link rel="stylesheet" href="css/style.css" />
    <script src="js/jquery-1.11.0.min.js"></script>
    <script src="js/script.js"></script>
  </head>

  <body>
    <div id="wrap">
      <h1>RG Thesis Uploader</h1>
      <form id="uploader" action="post.php" method="POST" enctype="multipart/form-data">
        <div class="form_item">
          <label>Login name:
            <input type="text" name="login_name" pattern="^[a-zA-Z0-9]+$" placeholder="/^[a-zA-Z0-9]+$/" required autofocus />
          </label>
        </div>
        <div class="form_item">
          <label>
            <input type="radio" name="class" required />
            Bachelor thesis </label>
          <label>
            <input type="radio" name="class" required />
            Master thesis </label>
          <label>
            <input type="radio" name="class" required />
            Doctor thesis </label>
          <label>
            <input type="radio" name="class" required />
            Other(with peer review) </label>
          <label>
            <input type="radio" name="class" required />
            Other(without peear review) </label>
        </div>
        <div class="form_item">
          <label>Name(ja):
            <input type="text" name="name_ja" placeholder="Ex.) 山田 太郎" />
          </label>
          <label>Name(en/other):
            <input type="text" name="name_en" placeholder="Ex.) Taro Yamada" />
          </label>
        </div>
        <div class="form_item">
          <label>Faculty / Course:
            <input type="text" name="belong" placeholder="Ex.) ◯◯学部/Faculty of ..." required />
          </label>
        </div>
        <div class="form_item">
          <label>Title(ja):
            <input type="text" name="title_ja" placeholder="Ex.) hogefugaの実装と提案" />
          </label>
          <label>Title(en):
            <input type="text" name="title_en" placeholder="Ex.) Implementation of hogefuga for fugahoge" />
          </label>
        </div>
        <div class="form_item">
          <label>Description(ja):             <textarea name="description_ja" placeholder="Ex.) fugahogeのためのhogefugaを実装し、提案する。"></textarea> </label>
          <label>Description(en):             <textarea name="description_en" placeholder="Ex.) Implement hogefuga for fugahoge."></textarea> </label>
        </div>
        <div class="form_item">
          <label>Keywords(4-6):
            <input type="text" name="keywords[]" placeholder="Required" required />
            <input type="text" name="keywords[]" placeholder="Required" required />
            <input type="text" name="keywords[]" placeholder="Required" required />
            <input type="text" name="keywords[]" placeholder="Required" required />
            <input type="text" name="keywords[]" />
            <input type="text" name="keywords[]" />
          </label>
        </div>
        <div class="form_item">
          <label>Mail:
            <input type="email" name="mail" placeholder="hogefuga@sfc.wide.ad.jp" required />
          </label>
        </div>
        <div class="form_item">
          <label>File(pdf only):
            <input type="file" name="file" accept="application/pdf" required />
          </label>
        </div>
        <div class="form_item">
          <input type="submit" value="Submit" />
        </div>
      </form>
    </div>
  </body>
</html>
