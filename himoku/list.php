<?php
require_once '../common/DbManager.php';
require_once '../common/Encode.php';
try {
    $db = getDb();
    $sql = "SELECT id, 費目名, 入出金区分, メモ
            FROM 費目";
    $stt = $db->prepare($sql);
    $stt->execute();
} catch (PDOException $e) {
    die("エラーメッセージ: {$e->getMessage()}");
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <title>費目一覧 | 山田家の家計簿</title>
</head>
<body>
    <h2>費目一覧</h2>
    <div><a href="create_form.php" class="btn btn-primary ml-2">新作登録</a></div>
    <table class="table">
        <thead>
            <tr><th>id</th><th>費目名</th><th>入出金区分</th><th>メモ</th><th></th></tr>
        </thead>
        <tbody>
        <?php while ($row = $stt->fetch(PDO::FETCH_ASSOC)) { ?>
            <?php // ['id' => 1, '費目名' => '食費', '入出金区分' => '出金', 'メモ' => 'ｘｘｘｘｘｘ'] ?>
            <tr>
                <td><?=e($row['id']) ?></td>
                <td><?=e($row['費目名']) ?></td>
                <td><?=e($row['入出金区分']) ?></td>
                <td><?=e($row['メモ']) ?></td>
                <td>
                    <a href="update_form.php?id=<?=e($row['id']) ?>" class="btn btn-secondary wl-2">編集</a>
                    <a href="delete_form.php?id=<?=e($row['id']) ?>" class="btn btn-danger wl-2">削除</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</body>
</html>