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
<a href="https://github.com/OHMORIYUSUKE/php-imas-vercel" class="github-corner" aria-label="View source on GitHub"><svg width="80" height="80" viewBox="0 0 250 250" style="fill:#151513; color:#fff; position: absolute; top: 0; border: 0; right: 0;" aria-hidden="true"><path d="M0,0 L115,115 L130,115 L142,142 L250,250 L250,0 Z"/><path d="M128.3,109.0 C113.8,99.7 119.0,89.6 119.0,89.6 C122.0,82.7 120.5,78.6 120.5,78.6 C119.2,72.0 123.4,76.3 123.4,76.3 C127.3,80.9 125.5,87.3 125.5,87.3 C122.9,97.6 130.6,101.9 134.4,103.2" fill="currentColor" style="transform-origin: 130px 106px;" class="octo-arm"/><path d="M115.0,115.0 C114.9,115.1 118.7,116.5 119.8,115.4 L133.7,101.6 C136.9,99.2 139.9,98.4 142.2,98.6 C133.8,88.0 127.5,74.4 143.8,58.0 C148.5,53.4 154.0,51.2 159.7,51.0 C160.3,49.4 163.2,43.6 171.4,40.1 C171.4,40.1 176.1,42.5 178.8,56.2 C183.1,58.6 187.2,61.8 190.9,65.4 C194.5,69.0 197.7,73.2 200.1,77.6 C213.8,80.2 216.3,84.9 216.3,84.9 C212.7,93.1 206.9,96.0 205.4,96.6 C205.1,102.4 203.0,107.8 198.3,112.5 C181.9,128.9 168.3,122.5 157.7,114.1 C157.9,116.9 156.7,120.9 152.7,124.9 L141.0,136.5 C139.8,137.7 141.6,141.9 141.8,141.8 Z" fill="currentColor" class="octo-body"/></svg></a><style>.github-corner:hover .octo-arm{animation:octocat-wave 560ms ease-in-out}@keyframes octocat-wave{0%,100%{transform:rotate(0)}20%,60%{transform:rotate(-25deg)}40%,80%{transform:rotate(10deg)}}@media (max-width:500px){.github-corner:hover .octo-arm{animation:none}.github-corner .octo-arm{animation:octocat-wave 560ms ease-in-out}}</style>
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
