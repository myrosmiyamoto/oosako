<?php
session_start();
require 'header.php';
require_once 'includes/db_connect.php';// データベース接続ファイルをインクルード
?>

<main>
    <?php if (isset($_SESSION['user'])): ?>
        <p>ようこそ、<?php echo htmlspecialchars($_SESSION['user']['login']); ?>さん</p>
        <!--画像保存ボタン-->
        <p>
        <form action="save.php" method="post">
            <button type="submit">新しいデザインを保存</button>
        </form>
        </p>

        <!--閲覧ボタン-->
        <p>
        <form action="view.php" method="post">
            <button type="submit">今までのデザインを探す</button>
        </form>
        </p>
        <!--ログアウトボタン-->
        <form action="logout.php" method="post">
            <button type="submit">ログアウト</button>
        </form>
        
    <?php endif; ?>
</main>

<?php require 'footer.php' ?>