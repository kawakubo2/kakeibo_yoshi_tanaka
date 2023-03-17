<?php
require_once '../common/DbManager.php';
require_once '../common/Encode.php';

try {
    $db = getDb(); // ネットワーク接続の確立
    $sql = "SELECT id, 費目名, 入出金区分, メモ
            FROM 費目
            WHERE id = :id"; // :xx ---> プレイスホルダ (後からデータを埋め込むという意味)
    $stt = $db->prepare($sql);
    $stt->bindValue(':id', $_GET['id']);
    $stt->execute();
    if ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
        ;
    } else {
        die('該当する費目が存在しません。');
    }
} catch (PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <title>削除 | 山田家の家計簿</title>
</head>
<body>
    <h2>費目の削除</h2>
    <div><a href="list.php" class="btn btn-success ml-2">費目一覧へ戻る</a></div> 
    <table class="table">
        <tr><th>費目名</th><td><?=e($row['費目名']) ?></td></tr>
        <tr><th>入出金区分</th><td><?=e($row['入出金区分']) ?></td></tr>
        <tr><th>メモ</th><td><?=e($row['メモ']) ?></td></tr>
    </table>
    <form method="POST" action="delete_process.php">
        <input type="hidden" name="id" value="<?=e($row['id']) ?>" />
        <input type="submit" name="delete" value="削除" />
        <input type="submit" name="cancel" value="中止" />
    </form>
</body>
</html>