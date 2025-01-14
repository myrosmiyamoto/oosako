<?php
session_start();
require 'header.php';
require_once 'includes/db_connect.php';
?>

<h1>ネイルデザインアルバム</h1>
<h2>ログイン</h2>

<?php
$login = filter_input(INPUT_POST, 'login');
$password = filter_input(INPUT_POST, 'password');

if (empty($login) || empty($password)) {
    echo '<p style="color: red;">ログイン名とパスワードを入力してください。</p>';
    echo '<form action="login-input.php" method="post">';
    echo '<input type="submit" value="戻る">';
    echo '</form>';
} else {
    try {

        $sql = $pdo->prepare('SELECT * FROM users WHERE login = ?');
        $sql->execute([$login]);
        $user = $sql->fetch();

        //password_verify()関数を使用して、
        //ユーザーが入力した元のパスワード（$password）と
        //データベースに保存されているハッシュ化された
        //パスワード（$user['password']）を比較
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = ['id' => $user['id'], 'login' => $user['login']];
            echo '<p>ログインしました。</p>';
            echo '<a href="home.php">ホームへ</a>';
        } else {
            echo '<p style="color: red;">ログイン名またはパスワードが違います。</p>';
            echo '<form action="login-input.php" method="post">';
            echo '<input type="submit" value="戻る">';
            echo '</form>';
        }
    } catch (PDOException $e) {
        echo "データベースエラー: " . $e->getMessage();
    }
}
?>

<?php require 'footer.php' ?>