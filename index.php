<?php

  $dataFile = 'bbs.dat';

  // CSRF対策
  session_start();

  function setToken(){
    $token = sha1(uniqid(mt_rand(),true));
    $_SESSION['token'] = $token;
  }

  function checkToken(){
    if (empty($_SESSION['token']) || ($_SESSION['token'] != $_POST['token'])) {
      echo "不正な POST が行われました";
      exit;
    }
  }

  function h($s){
    return htmlspecialchars($s,ENT_QUOTES,'UTF-8');
  }

if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
    isset($_POST['name']) &&
    isset($_POST['title']) &&
    isset($_POST['message'])
 ) {

  checkToken();

  $name = trim($_POST['name']);
  $title = trim($_POST['title']);
  $message = trim($_POST['message']);

  if ($message !== '') {
    $name = ($name === '') ? 'no name' : $name;
    $title = ($title === '') ? 'no title' : $title;
    $message = str_replace("\t", " ", $message);
    $name = str_replace("\t", " ", $name);
    $title = str_replace("\t", " ", $title);
    $postedAt = date('Y-m-d H:i:s');

    $newData = $name . "\t" . $title . "\t" . $message . "\t" . $postedAt . "\n";

    $fp = fopen($dataFile, 'a');
    fwrite($fp, $newData);
    fclose($fp);
  } else {
    setToken();
  }
}

$posts = file($dataFile,FILE_IGNORE_NEW_LINES);
$posts = array_reverse($posts);

 ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>簡易掲示板</title>
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="container">
    <section class="form">
      <header>
        <h1>簡易掲示板</h1>
      </header>
      <main>
        <form action="" method="post" class="form-parts">
          <label for="name">名前</label>
          <input id="name" type="text" name="name" autocomplete="off" placeholder="あなたの名前">
          <label for="title">タイトル</label>
          <input id="title" type="text" name="title" autocomplete="off" placeholder="タイトルを入力">
          <label for="message">メッセージ</label>
          <textarea id="message" name="message" placeholder="メッセージを入力"></textarea>
          <button class="submit">送信</button>
          <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
        </form>
      </main>
    </section><!-- /form -->
    <?php if (count($posts)): ?>
      <section class="posts-container">
      <?php foreach($posts as $post) : ?>
      <?php list($name,$title,$message,$postedAt) = explode("\t", $post); ?>
        <article class="box">
          <div class="content-column">
            <h2>タイトル: <?= h($title); ?></h2>
            <span class="post-user">投稿者: <?= h($name); ?> </span>
            <span class="post-date">投稿日: <?= h($postedAt); ?> </span>
          </div><!-- /content-column -->
          <div class="message-column">
            <h3>メッセージ: </h3>
            <p><?= h($message); ?></p>
          </div><!-- /message-column -->
        </article><!-- /box -->
    <?php endforeach; ?>
    </section><!-- /posts-container -->
    <?php else: ?>
    <section class="no-post">
      <h2>投稿はまだありません</h2>
    </section><!-- /no-post -->
    <?php endif; ?>
  </div><!-- /container -->

</body>
</html>