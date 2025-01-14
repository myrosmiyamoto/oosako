<?php
session_start();
require 'header.php';
require_once 'includes/db_connect.php';// データベース接続ファイルをインクルード

// ユーザーがログインしているか確認
if (!isset($_SESSION['user'])) {
    header('Location: login-input.php');
    exit;
}

var_dump($_POST);

$login = $_SESSION['user']['login'];

if (!empty($_SESSION['selected_images'])) {
    try {
        // トランザクション開始
        $pdo->beginTransaction();

        // グループIDを生成
        $groupId = uniqid('group_');

        foreach ($_SESSION['selected_images'] as $image_path) {
            // MIMEタイプを取得
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $image_path);
            finfo_close($finfo);

            //{$login}_centersに情報を保存
            //作る

            // {$login}_imagesに画像情報を保存
            $stmt = $pdo->prepare("INSERT INTO {$login}_images (image_path, group_id, mime_type) VALUES (?, ?, ?)");
            $stmt->execute([$image_path, $groupId, $mime_type]);
            $image_id = $pdo->lastInsertId();


            //属性を保存
            $attribute_types = ['colors', 'moods', 'seasons'];
            foreach ($attribute_types as $type) {
                $selected_attribute = "selected_{$type}";
                // セッションにデータが存在し、配列であることを確認
                if (isset($_POST[$selected_attribute]) && is_array($_POST[$selected_attribute])) {
                    // 選択された属性がある場合のみ処理を行う
                    if (!empty($_POST[$selected_attribute])) {
                        foreach ($_POST[$selected_attribute] as $selected_num) {
                            // 数値であることを確認（簡易的なバリデーション）
                            if (is_numeric($selected_num)) {
                                try {
                                    // 属性を保存するためのSQL文
                                    $stmt = $pdo->prepare("INSERT INTO {$login}_image_attributes (group_id, attribute_type, attribute_id) VALUES (?, ?, ?)");
                                    $stmt->execute([$groupId, rtrim($type, 's'), $selected_num]); // 属性タイプを単数形に変換
                                } catch (PDOException $e) {
                                    echo "データ挿入エラー: " . $e->getMessage();
                                }
                            }
                        }
                    }
                }
            }
        }
        // トランザクションをコミット
        $pdo->commit();

        // セッションから選択された画像を削除
        unset($_SESSION['selected_images']);

        echo "<p>全ての画像と属性が正常に保存されました。</p>";
        echo "<a href='home.php'>ホームへ</a>";
    } catch (PDOException $e) {
        // エラーが発生した場合はロールバック
        $pdo->rollBack();
        echo "データベースエラー: " . $e->getMessage();
    }
} else {
    echo "<p>保存する画像がありません。</p>";
    echo "<a href='save.php'>画像選択に戻る</a>";
}

require 'footer.php';
?>
