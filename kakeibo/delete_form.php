<?php
require_once '../common/DbManager.php';
require_once '../common/Encode.php';

try {
    $db = getDb();
    $sql = "SELECT K.id, K.日付, H.費目名, K.メモ, K.入金額, K.出金額
            FROM 家計簿 AS K
                INNER JOIN 費目 AS H ON K.費目id = H.id
            WHERE K.id = :id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':id', $_GET['id']);
    $stt->execute();
    if ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
        ;
    } else {
        die('該当する家計簿がありません。');
    }
} catch (PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <title>Document</title>
</head>
<body>
    <h2>家計簿の削除</h2>
    <div><a href="list.php" class="btn btn-success ml-2">家計簿一覧へ戻る</a></div>
    <table class="table table-striped">
        <tbody>
            <tr><th>id</th><td><?=e($row['id']) ?></td></tr>
            <tr><th>日付</th><td><?=e($row['日付']) ?></td></tr>
            <tr><th>費目名</th><td><?=e($row['費目名']) ?></td></tr>
            <tr><th>メモ</th><td><?=e($row['メモ']) ?></td></tr>
            <tr><th>入金額</th><td><?=e($row['入金額']) ?></td></tr>
            <tr><th>出金額</th><td><?=e($row['出金額']) ?></td></tr>
        </tbody>
    </table>
    <form method="POST" action="delete_process.php">
        <input type="hidden" name="id" value="<?=e($row['id']) ?>" />
        <input type="submit" name="delete" value="削除" />
        <input type="submit" name="cancel" value="中止" />
    </form>
</body>
</html>