<?php
// Database connection setup
$host = $_ENV['POSTGRES_HOST'];
$port = $_ENV['POSTGRES_PORT'];
$dbname = $_ENV['POSTGRES_DATABASE'];
$user = $_ENV['POSTGRES_USER'];
$password = $_ENV['POSTGRES_PASSWORD'];

$connection_string = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
    $dbh = new PDO($connection_string, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    echo "データベース接続エラー: " . $e->getMessage();
}

// Parameters for search
$ch_name = $_GET['ch-name'] ?? "";
$cv_name = $_GET['cv-name'] ?? "";
$ch_blood_type = $_GET['ch-blood-type'] ?? "";
$type = $_GET['group'] ?? "";

function escape_html($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Display the HTML header
header("Content-Type: text/html; charset=UTF-8");
echo "<html lang='ja'>";
echo "<head><title>アイドル名簿</title><link rel='stylesheet' href='style/main.css'></head>";
echo "<body>";


?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アイドル名簿</title>
    <link rel="stylesheet" href="style/main.css">
    <style>
        /* コンテナを横並びにする */
        .result-item {
            display: flex;            /* 横並び */
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
            margin-bottom: 10px;      /* アイテム間のスペース */
        }

        .result-item img {
            width: 100px;             /* 画像のサイズ調整 */
            height: 100px;
            margin-left: 20px;
            margin-right: 20px;       /* 画像とテキスト間のスペース */
        }

        p {
            margin: 5px;
            padding: 0;
        }

        .result-item .character {
            display: flex;
            flex-direction: column;   /* テキストを縦に並べる */
        }

        /* その他のスタイル */
        .blocktext {
            margin-left: auto;
            margin-right: auto;
            max-width: 60%;
            min-width: 20%;
            overflow-wrap: break-word;
            border: rgb(0, 0, 0) 1px solid;
        }

        .container {
            display: flex;
        }

        /* フォームのスタイル */
        .search-form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<?php

echo "<div class='blocktext'>";
echo '<div class="container">';
echo '<form action="./" method="get" class="container">';

// Display the form
echo '<div style="margin: 10px">';
echo "<h5>アイドルの名前</h5>";
echo '<input type="text" name="ch-name" value="' . escape_html($ch_name) . '">';
echo "</div>";

echo '<div style="margin: 10px">';
echo "<h5>声優の名前</h5>";
echo '<input type="text" name="cv-name" value="' . escape_html($cv_name) . '">';
echo "</div>";

echo '<div style="margin: 10px">';
echo "<h5>血液型</h5>";
echo '<select id="ch_blood_type" name="ch-blood-type">';
echo "<option value=''>指定なし</option>";

// Query distinct blood types
$bloodTypeStmt = $dbh->query("SELECT DISTINCT ch_blood_type FROM imas_characters");
foreach ($bloodTypeStmt as $row) {
    $selected = ($row['ch_blood_type'] === $ch_blood_type) ? ' selected' : '';
    echo "<option value='" . escape_html($row['ch_blood_type']) . "'$selected>" . escape_html($row['ch_blood_type']) . "</option>";
}
echo "</select>";
echo "</div>";

echo '<div style="margin: 10px">';
echo "<h5>グループ</h5>";
echo '<select id="type" name="group">';
echo "<option value=''>指定なし</option>";

// Query distinct groups
$groupStmt = $dbh->query("SELECT DISTINCT type FROM imas_characters");
foreach ($groupStmt as $row) {
    $selected = ($row['type'] === $type) ? ' selected' : '';
    echo "<option value='" . escape_html($row['type']) . "'$selected>" . escape_html($row['type']) . "</option>";
}
echo "</select>";
echo "</div>";

echo '<div style="margin: 10px; margin-top: auto;">';
echo "<button>検索</button>";
echo "</div>";

echo "</form>";
echo "</div>";
echo "</div>";
?>

<div class="blocktext" style="margin-top: 10px;">

<?php
// Display search results
$ch_name = empty($ch_name) ? '%' : $ch_name;
$cv_name = empty($cv_name) ? '%' : $cv_name;
$ch_blood_type = empty($ch_blood_type) ? '%' : $ch_blood_type;
$type = empty($type) ? '%' : $type;

$sql_query = ($ch_name === '' && $cv_name === '' && $ch_blood_type === '%' && $type === '%') ?
    "SELECT ic.*, ici.image FROM imas_characters ic LEFT JOIN imas_characters_image ici ON ic.ch_first_name = ici.name" :
    "SELECT ic.*, ici.image FROM imas_characters ic LEFT JOIN imas_characters_image ici ON ic.ch_first_name = ici.name
     WHERE (ic.ch_name LIKE ? OR ic.ch_name_ruby LIKE ?) 
     AND (ic.cv_name LIKE ? OR ic.cv_name_ruby LIKE ?) 
     AND ic.ch_blood_type LIKE ? 
     AND ic.type LIKE ?";

$stmt = $dbh->prepare($sql_query);

if ($ch_name === '' && $cv_name === '' && $ch_blood_type === '%' && $type === '%') {
    $stmt->execute();
} else {
    $stmt->execute(["%$ch_name%", "%$ch_name%", "%$cv_name%", "%$cv_name%", $ch_blood_type, $type]);
}

if ($stmt->rowCount() == 0) {
    echo "<p class='character'>結果がありません。</p>";
} else {
    foreach ($stmt as $row) {
        echo "<div class='result-item'>";
        
        // 画像がある場合に表示
        if (!empty($row['image'])) {
            echo "<img src='" . escape_html($row['image']) . "' alt='" . escape_html($row['ch_name']) . "' />";
        }

        echo "<div class='character'>";
        echo "<p><b>" . escape_html($row['id']) . "</b>　" . escape_html($row['type']) . "</p>";
        echo "<p><ruby><rb>" . escape_html($row['ch_name']) . "<rb><rp>（</rp><rt>" . escape_html($row['ch_name_ruby']) . "</rt><rp>）</rp></ruby>　" .
            escape_html($row['ch_birth_month']) . "月" . escape_html($row['ch_birth_day']) . "日生まれ　" .
            ($row['ch_gender'] == 1 ? "女性" : "男性") . "　" .
            ($row['is_idol'] == 1 ? "アイドル" : "アイドル以外") . "　" .
            escape_html($row['ch_blood_type']) . "型</p>";

        if (!empty($row['cv_name'])) {
            echo "<p>CV：<ruby><rb>" . escape_html($row['cv_name']) . "<rb><rp>（</rp><rt>" . escape_html($row['cv_name_ruby']) . "</rt><rp>）</rp></ruby>　" .
                escape_html($row['cv_birth_month']) . "月" . escape_html($row['cv_birth_day']) . "日生まれ　" .
                ($row['cv_gender'] == 1 ? "女性" : "男性") . "</p>";
        }

        echo "</div>"; // character div
        echo "</div>"; // result-item div
        echo "<hr>";
    }
}
?>

</div>
</body>
</html>
