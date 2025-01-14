<?php require 'header.php' ?>
<h1>宮本の編集</h1>
<h1>ネイルデザインアルバム</h1>
<h2>ログイン</h2>
<form action="login-output.php" method="post">
    ログイン名<input type="text" name="login"><br>
    パスワード<input type="password" name="password"><br>
    <input type="submit" value="ログイン">
</form>
<hr>
<p>ネイルデザインアルバムのご利用にはユーザー登録が必要です。<br>未登録の方はこちらから登録してご利用ください。</p>
<p><form action="sign_up-input.php" method="post">
    <input type="submit" value="新規登録する">
</form></p>

<?php require 'footer.php' ?>
