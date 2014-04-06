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
          <label><span class="styled_label">Login name:</span>
            <input type="text" name="login_name" pattern="^[a-zA-Z0-9]+$" placeholder="/^[a-zA-Z0-9]+$/" required autofocus />
          </label>
        </div>
        <div class="form_item">
          Class for thesis:
          <div class="radio">
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
        </div>
        <div class="form_item">
          <label><span class="styled_label">Name(ja):</span>
            <input type="text" name="name_ja" placeholder="Ex.) 山田 太郎" />
          </label>
          <label><span class="styled_label">Name(en/other):</span>
            <input type="text" name="name_en" placeholder="Ex.) Taro Yamada" />
          </label>
        </div>
        <div class="form_item">
          <label><span class="styled_label">Faculty / Course:</span>
            <input type="text" name="belong" placeholder="Ex.) ◯◯学部/Faculty of ..." required />
          </label>
        </div>
        <div class="form_item">
          <label><span class="styled_label">Title(ja):</span>
            <input type="text" name="title_ja" placeholder="Ex.) hogefugaの実装と提案" />
          </label>
          <label><span class="styled_label">Title(en/other):</span>
            <input type="text" name="title_en" placeholder="Ex.) Implementation of hogefuga for fugahoge" />
          </label>
        </div>
        <div class="form_item">
          <label><span class="styled_label">Description(ja):</span>             <textarea name="description_ja" placeholder="Ex.) fugahogeのためのhogefugaを実装し、提案する。"></textarea> </label>
          <label><span class="styled_label">Description(en/other):</span>             <textarea name="description_en" placeholder="Ex.) Implement hogefuga for fugahoge."></textarea> </label>
        </div>
        <div class="form_item">
          <label><span class="styled_label">Keywords(4-6):</span>
            <div id="keywords_box">
              <input type="text" class="keywords" name="keywords[]" placeholder="Required" required />
              <input type="text" class="keywords" name="keywords[]" placeholder="Required" required />
              <input type="text" class="keywords" name="keywords[]" placeholder="Required" required />
              <input type="text" class="keywords" name="keywords[]" placeholder="Required" required />
              <input type="text" class="keywords" name="keywords[]" placeholder="Option" />
              <input type="text" class="keywords" name="keywords[]" placeholder="Option" />
            </div> </label>
        </div>
        <div class="form_item">
          <label><span class="styled_label">Mail:</span>
            <input type="email" name="mail" placeholder="fuga@sfc.wide.ad.jp" required />
          </label>
        </div>
        <div class="form_item">
          <label><span class="styled_label">File(pdf only):</span>
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
