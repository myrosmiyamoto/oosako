<?php
session_start();
require 'header.php';
require_once 'includes/db_connect.php';
?>

<h1>ネイルデザインアルバム</h1>
<h2>新規登録</h2>

<?php
$login = filter_input(INPUT_POST, 'login');
$password = filter_input(INPUT_POST, 'password');

if (empty($login) || empty($password)) {
    echo '<p style="color: red;">ログイン名とパスワードを入力してください。</p>';
    echo '<form action="sign_up-input.php" method="post">';
    echo '<input type="submit" value="戻る">';
    echo '</form>';
} else {
    try {
        // $pdo = new PDO('mysql:host=localhost;dbname=nda;charset=utf8', 'staff', 'password');
        // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = $pdo->prepare('SELECT * FROM users WHERE login = ?');
        $sql->execute([$login]);

        if (empty($sql->fetchAll())) {
            // ユーザーを登録（パスワードをハッシュ化して保存）
            $sql = $pdo->prepare('INSERT INTO users VALUES (null, ?, ?)');
            $sql->execute([$login, password_hash($password, PASSWORD_DEFAULT)]);
            $userId = $pdo->lastInsertId();

            // ユーザー固有のテーブルを作成する部分を以下のように変更
            $pdo->exec("CREATE TABLE {$login}_images (
            id INT AUTO_INCREMENT PRIMARY KEY,
            image_path VARCHAR(255),
            group_id VARCHAR(50),
            mime_type VARCHAR(100),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");

            //group_idの保存と一言メモ的なテキスト保存用テーブル
            $pdo->exec("CREATE TABLE {$login}_centers(
                id INT AUTO_INCREMENT PRIMARY KEY,
                group_id VARCHAR(50),
                text VARCHAR(255)
                )");

            // 色、ムード、季節のテーブルはそのまま保持
            $pdo->exec("CREATE TABLE {$login}_colors (id INT AUTO_INCREMENT PRIMARY KEY, color VARCHAR(50))");
            $pdo->exec("CREATE TABLE {$login}_moods (id INT AUTO_INCREMENT PRIMARY KEY, mood VARCHAR(50))");
            $pdo->exec("CREATE TABLE {$login}_seasons (id INT AUTO_INCREMENT PRIMARY KEY, season VARCHAR(50))");

            // 画像と各属性の関連付けテーブルを作成
            //外部キー制約に ON DELETE CASCADE を追加することで、関連する画像が削除された場合に、このテーブルのデータも自動的に削除される
            $pdo->exec("CREATE TABLE {$login}_image_attributes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                group_id VARCHAR(50),
                attribute_type ENUM('color', 'mood', 'season') NOT NULL,
                attribute_id INT
                )");
                //↓DB保存後に削除を連動させる仕組みだがgroup_idでは不可能なので他の方法を考える。
                // FOREIGN KEY (group_id) REFERENCES {$login}_images(group_id) ON DELETE CASCADE


            // colorsテーブルに初期値を挿入
            $colors = ['ブラック', 'ホワイト','パープル', 'ブルー', 'グリーン', 'イエロー', 'ベージュ', 'オレンジ', 'ピンク', 'レッド', 'ブラウン', 'ゴールド', 'シルバー', 'コッパー'];
            $stmt = $pdo->prepare("INSERT INTO {$login}_colors (color) VALUES (?)");
            foreach ($colors as $color) {
                $stmt->execute([$color]);
            }

            // moodsテーブルに初期値を挿入
            $moods = ['オフィス', 'フェミニン', 'ハード', 'モード', 'ナチュラル'];
            $stmt = $pdo->prepare("INSERT INTO {$login}_moods (mood) VALUES (?)");
            foreach ($moods as $mood) {
                $stmt->execute([$mood]);
            }

            // seasonsテーブルに初期値を挿入
            $seasons = ['春', '夏', '秋', '冬', '梅雨', '雪', '桜'];
            $stmt = $pdo->prepare("INSERT INTO {$login}_seasons (season) VALUES (?)");
            foreach ($seasons as $season) {
                $stmt->execute([$season]);
            }

            $_SESSION['user'] = ['id' => $userId, 'login' => $login];
            echo '<p>登録が完了しました。</p>';
            echo '<a href="home.php">ホームへ</a>';
        } else {
            echo '<p style="color: red;">このログイン名はご利用いただけません。別の名前を選んでください。</p>';
            echo '<form action="sign_up-input.php" method="post">';
            echo '<input type="submit" value="戻る">';
            echo '</form>';
        }
    } catch (PDOException $e) {
        echo "データベースエラー: " . $e->getMessage();
    }
}
?>

<?php require 'footer.php' ?>
