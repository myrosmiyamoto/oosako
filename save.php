<?php
session_start();
?>
<!-- ホームに戻るボタン -->
<form action="home.php" method="get">
    <input type="submit" value="ホームに戻る">
</form>
<?php
//session_start();
require 'header.php';
require_once 'includes/db_connect.php';// データベース接続ファイルをインクルード

/*
1. セッションにselected_images配列を追加し、選択された画像のパスを保存します。
2. 画像がアップロードされるたびに、その画像のパスをセッションの配列に追加します。
3. 選択された画像をブラウザに表示します。
4. 「全ての画像を保存」ボタンを追加し、クリックするとsave_to_db.phpに遷移します。
*/

// ユーザーがログインしているか確認
if (!isset($_SESSION['user'])) {
    header('Location: login-input.php');
    exit;
}
echo "<p>保存画面：" . htmlspecialchars($_SESSION['user']['login']) . "さんでログイン中</p>";

$login = $_SESSION['user']['login'];

// セッションに選択された画像の配列を初期化
if (!isset($_SESSION['selected_images'])) {
    $_SESSION['selected_images'] = [];
}

// 画像アップロードの処理
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . uniqid() . '_' . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
            $_SESSION['selected_images'][] = $uploadFile;
            echo "<p>画像がアップロードされました。</p>";
        } else {
            echo "<p>ファイルのアップロードに失敗しました。</p>";
        }
    } else {
        echo "<p>ファイルアップロードエラー: " . $file['error'] . "</p>";
    }
}

// 画像削除の処理
if (isset($_POST['delete_image'])) {
    $index = $_POST['delete_image'];
    if (isset($_SESSION['selected_images'][$index])) {
        unlink($_SESSION['selected_images'][$index]); // ファイルを削除
        unset($_SESSION['selected_images'][$index]);
        $_SESSION['selected_images'] = array_values($_SESSION['selected_images']); // インデックスを再整理
    }
}

print_r($_SESSION);

// 画像アップロードフォーム
?>
<form action="save.php" method="post" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <input type="submit" value="画像をアップロード">
</form>

<h3>選択された画像</h3>
<?php
if (!empty($_SESSION['selected_images'])) {
    foreach ($_SESSION['selected_images'] as $index => $image) {
        echo "<div style=display: inline-block; margin: 10px; text-align: center;>";
        echo "<img src='" . htmlspecialchars($image) . "' width='200'><br>";
        //アップロードされた各画像に削除ボタンをつける
        echo "<form action='save.php' method='post'>";
        echo "<input type='hidden' name='delete_image' value='$index'>";
        echo "<input type='submit' value='削除'>";
        echo "</form></div>";
    }

    //hiddenでブラウザに表示せず
    //画像のインデックス'image_index'を'$index'に保持 save_to_db.php
    echo "<form action='save_to_db.php' method='post'>";
    echo "<input type='hidden' name='image_index' value='$index'>";

    // 属性選択フォーム
    // 色の選択
    echo "<h4>色を選択：</h4>";
    $stmt = $pdo->query("SELECT * FROM {$login}_colors");
    while ($color = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<label>";
        echo "<input type='checkbox' name='selected_colors[]' value='" . $color['id'] . "'>";
        echo htmlspecialchars($color['color']);
        echo "</label><br>";
    }

    // ムードと季節も同様に追加
    // 雰囲気
    echo "<h4>雰囲気を選択：</h4>";
    $stmt = $pdo->query("SELECT * FROM {$login}_moods");
    while ($mood = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<label>";
        echo "<input type='checkbox' name='selected_moods[]' value='" . $mood['id'] . "'>";
        echo htmlspecialchars($mood['mood']);
        echo "</label><br>";
    }
    // 季節
    echo "<h4>季節を選択：</h4>";
    $stmt = $pdo->query("SELECT * FROM {$login}_seasons");
    while ($season = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<label>";
        echo "<input type='checkbox' name='selected_seasons[]' value='" . $season['id'] . "'>";
        echo htmlspecialchars($season['season']);
        echo "</label><br>";
    }

    // 全ての画像を保存するボタンsave_to_db.php
    //echo "<form action='save2.php' method='post'>";
    echo "<input type='submit' value='画像を保存'>";
    echo "</form>";
} else {
    echo "<p>選択された画像はありません。</p>";
}
?>

<?php require 'footer.php'; ?>