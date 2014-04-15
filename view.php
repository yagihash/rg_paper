<?php
require_once (__DIR__ . "/common.php");
require_once (__DIR__ . "/db/DBinterface.php");

$DBinterface = new DBinterface();
$reader = $DBinterface -> reader;
$result = $reader -> getPapers();
?>
<!DOCTYPE html>

<html lang="ja">
  <head>
    <meta charset="UTF-8" />
    <title>RG Thesis Uploader - Viewer</title>
    <link rel="stylesheet" href="css/style.css" />
    <script src="js/jquery-1.11.0.min.js"></script>
    <script src="js/script.js"></script>
  </head>

  <body>
    <div id="wrap">
      <h1>RG Thesis Uploader - Viewer</h1>
<?php
foreach($result as $paper):
  $user = $reader -> getUser($paper["user_id"]);
?>
      <div class="paper">
        <div class="paper_item">
          <span class="label">Login name:</span><span class="login_name"><?php echo escapeHTML($user["login_name"]); ?></span>
        </div>
        <div class="paper_item">
          <span class="label">Name:</span><span class="name ja"><?php echo escapeHTML($user["name_ja"]); ?></span>/<span class="name en"><?php echo escapeHTML($user["name_en"]); ?></span>
        </div>
        <div class="paper_item">
          <span class="label">Class:</span><span class="class"><?php echo escapeHTML($paper["class"]); ?></span>
        </div>
        <div class="paper_item">
          <span class="label">Belong:</span><span class="belong"><?php echo escapeHTML($user["belong"]); ?></span>
        </div>
        <div class="paper_item">
          <span class="label">Title:</span><span class="title ja"><?php echo escapeHTML($paper["title_ja"]); ?></span>/<span class="title en"><?php echo escapeHTML($paper["title_en"]); ?></span>
        </div>
        <div class="paper_item">
          <div class="label description">Description:</div><p class="description ja"><?php echo str_replace("\n", "<br />\n", escapeHTML($paper["description_ja"])); ?></p>
          <p class="description en"><?php echo str_replace("\n", "<br />\n", escapeHTML($paper["description_en"])); ?></p>
        </div>
        <div class="paper_item">
          <span class="label">Keywords:</span>
          <ul class="keywords">
<?php
    foreach(unserialize($paper["keywords"]) as $value):
?>
            <li><?php echo escapeHTML($value); ?></li>
<?php
endforeach;
?>
          </ul>
        </div>
        <div class="paper_item">
          <span class="label">Mail:</span><span class="mail"><?php echo escapeHTML($user["mail"]); ?></span>
        </div>
        <div class="paper_item">
          <span class="label">File:</span><span class="file"><a href="./file.php?file=<?php echo escapeHTML($paper["file_name"]) ?>">FILE</a></span>
        </div>
      </div>
      <hr />
<?php
endforeach;
?>
    </div>
  </body>
</html>
