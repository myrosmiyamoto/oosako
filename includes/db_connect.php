<?php
// データベース接続情報
define('DB_HOST', 'database:3306');
define('DB_NAME', 'nda');
define('DB_USER', 'staff');
define('DB_PASS', 'password');

try {
    // PDOオブジェクトの作成
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    // 接続成功時のメッセージ（デバッグ用、本番環境では削除してください）
    // echo "データベースに接続しました。";

} catch (PDOException $e) {
    // エラーハンドリング
    echo "接続エラー: " . $e->getMessage();
}

// グローバル変数$pdoが他のファイルで使用可能になります
