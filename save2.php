<?php
session_start();
require 'header.php';
require_once 'includes/db_connect.php';// データベース接続ファイルをインクルード
// ユーザーがログインしているか確認
if (!isset($_SESSION['user'])) {
    header('Location: login-input.php');
    exit;
}
$login = $_SESSION['user']['login'];

//**********
print_r($_POST);
echo"<br>";
//**********
print_r($_SESSION);


require 'footer.php';
?>
