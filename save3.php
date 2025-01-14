<?php
session_start();
require '../header.php';

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

// 画像削除の処理
if (isset($_POST['delete_image'])) {
    $index = $_POST['delete_image'];
    if (isset($_SESSION['selected_images'][$index])) {
        unlink($_SESSION['selected_images'][$index]); // ファイルを削除
        unset($_SESSION['selected_images'][$index]);
        $_SESSION['selected_images'] = array_values($_SESSION['selected_images']); // インデックスを再整理
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    try {
        $file = $_FILES['file'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileError = $file['error'];

        if ($fileError === UPLOAD_ERR_OK) {
            $fileDestination = '../uploads/' . uniqid() . '_' . $fileName;
            move_uploaded_file($fileTmpName, $fileDestination);

            // セッションに画像情報を追加
            $_SESSION['selected_images'][] = $fileDestination;

            echo "<p>画像が正常にアップロードされました。</p>";
        } else {
            echo "<p>ファイルのアップロード中にエラーが発生しました。</p>";
        }
    } catch (Exception $e) {
        echo "エラー: " . $e->getMessage();
    }
}
?>

<a href="home.php">ホームへ</a>
<p>保存画面</p>
<p>保存する画像を選択する（複数選択可能）</p>
<form action="save.php" method="post" enctype="multipart/form-data">
    <p><input type="file" name="file" onchange="this.form.submit()"></p>
</form>

<!-- 選択された画像を表示 
 -->
<?php if (!empty($_SESSION['selected_images'])): ?>
    <h3>選択された画像：</h3>
    <?php foreach ($_SESSION['selected_images'] as $index => $image): ?>
        <div style="display: inline-block; margin: 10px; text-align: center;">
            <img src="<?php echo htmlspecialchars($image); ?>" alt="Selected Image" style="max-width: 200px; margin-bottom: 5px;">
            <form action="save.php" method="post">
                <input type="hidden" name="delete_image" value="<?php echo $index; ?>">
                <input type="submit" value="削除">
            </form>
        </div>
    <?php endforeach; ?>

    <form action="save_to_db.php" method="post">
        <input type="submit" value="全ての画像を保存">
    </form>
<?php endif; ?>

<?php require '../footer.php'; ?>