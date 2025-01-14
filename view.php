<?php
session_start();
require 'header.php';
require_once 'includes/db_connect.php';

// ユーザーがログインしているか確認
if (!isset($_SESSION['user'])) {
    header('Location: login-input.php');
    exit;
}
echo "<p>" . htmlspecialchars($_SESSION['user']['login']) . "さんでログイン中</p>";
$login = $_SESSION['user']['login'];

try {
    
    // それぞれテーブルの中身を取得してプルダウンで選択できるようにする。「卒業制作UI.pptx」参考

    //未選択＝＝全選択
//日付は「images」テーブルに保存されている
//色タグは「colors」テーブルに保存されている
//雰囲気タグは「moods」テーブルに保存されている
//季節タグは「seasons」テーブルに保存されている
//それぞれのテーブルの中身を取得する
    // ユーザー固有のテーブルから画像を取得
    $stmt = $pdo->prepare("SELECT * FROM {$login}_images");
    $stmt->execute();
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 日付、色、雰囲気、季節のタグを取得
    $dates = $pdo->query("SELECT DISTINCT DATE(created_at) as date FROM {$login}_images ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);//DESCで重複を削除,降順新先
    $colors = $pdo->query("SELECT * FROM {$login}_colors")->fetchAll(PDO::FETCH_ASSOC);
    $moods = $pdo->query("SELECT * FROM {$login}_moods")->fetchAll(PDO::FETCH_ASSOC);
    $seasons = $pdo->query("SELECT * FROM {$login}_seasons")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "データベースエラー: " . $e->getMessage();
    exit;
}
?>

<a href="home.php">ホームへ</a>
<p>閲覧画面</p>
<!-- 検索フォーム 各プルダウン内で値を複数選択できるようにする。検索ボタンを押さなくても、選択したプルダウン内の値に該当する画像が表示されるようにする。 -->
<form id="filterForm">
    <select name="dates[]" multiple>
        <option value="">日付を選択</option>
        <?php foreach ($dates as $date): ?>
            <option value="<?php echo htmlspecialchars($date['date']); ?>"><?php echo htmlspecialchars($date['date']); ?></option>
        <?php endforeach; ?>
    </select>

    <select name="colors[]" multiple>
        <option value="">色を選択</option>
        <?php foreach ($colors as $color): ?>
            <option value="<?php echo htmlspecialchars($color['id']); ?>"><?php echo htmlspecialchars($color['color']); ?></option>
        <?php endforeach; ?>
    </select>

    <select name="moods[]" multiple>
        <option value="">雰囲気を選択</option>
        <?php foreach ($moods as $mood): ?>
            <option value="<?php echo htmlspecialchars($mood['id']); ?>"><?php echo htmlspecialchars($mood['mood']); ?></option>
        <?php endforeach; ?>
    </select>

    <select name="seasons[]" multiple>
        <option value="">季節を選択</option>
        <?php foreach ($seasons as $season): ?>
            <option value="<?php echo htmlspecialchars($season['id']); ?>"><?php echo htmlspecialchars($season['season']); ?></option>
        <?php endforeach; ?>
    </select>
</form>

<div id="imageContainer"></div>

<script>
const images = <?php echo json_encode($images); ?>;

function filterImages() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    
    const filteredImages = images.filter(image => {
       const imageDate = new Date(image.created_at).toISOString().split('T')[0];
        const selectedDates = formData.getAll('dates[]');
        const selectedColors = formData.getAll('colors[]');
        const selectedMoods = formData.getAll('moods[]');
        const selectedSeasons = formData.getAll('seasons[]');

       
        return(selectedDates.length === 0 || selectedDates.includes(imageDate)) &&
        (selectedColors.length === 0 || image.colors.some(color => selectedColors.includes(color.toString()))) &&
               (selectedMoods.length === 0 || image.moods.some(mood => selectedMoods.includes(mood.toString()))) &&
               (selectedSeasons.length === 0 || image.seasons.some(season => selectedSeasons.includes(season.toString())));
    });

    displayImages(filteredImages);
}

function displayImages(images) {
    const container = document.getElementById('imageContainer');
    container.innerHTML = '';
    images.forEach(image => {
        const img = document.createElement('img');
        img.src = image.image_path;
        img.alt = 'Nail Design';
        img.style.maxWidth = '200px';
        img.style.margin = '10px';
        container.appendChild(img);
    });
}

document.querySelectorAll('select').forEach(select => {
    select.addEventListener('change', filterImages);
});

// 初期表示
filterImages();
</script>

<?php require 'footer.php'; ?>