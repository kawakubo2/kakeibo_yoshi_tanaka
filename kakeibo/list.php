<?php
require_once '../common/DbManager.php';
require_once '../common/Encode.php';

session_start();

if (!isset($_SESSION['year_month'])) {
    $_SESSION['year_month'] = new DateTime();
}
if (!isset($_SESSION['month_interval'])) {
    $_SESSION['month_interval'] = new DateInterval('P1M');
}

if (isset($_GET['back'])) {
    $_SESSION['year_month']->sub($_SESSION['month_interval']);
}
if (isset($_GET['go'])) {
    $_SESSION['year_month']->add($_SESSION['month_interval']);
}

try {
    $db = getDb();
    $sql = "SELECT K.id, K.日付, H.費目名, K.メモ, K.入金額, K.出金額
            FROM 家計簿 AS K
                INNER JOIN 費目 AS H ON K.費目id = H.id
            WHERE 
                YEAR(K.日付) = :year
                AND
                MONTH(K.日付) = :month
            ORDER BY 日付";
    $stt = $db->prepare($sql);
    $stt->bindValue(':year', $_SESSION['year_month']->format('Y'));
    $stt->bindValue(':month', $_SESSION['year_month']->format('m'));
    $stt->execute();
} catch(PDOException $e) {
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
    <title>山田さんちの家計簿</title>
</head>
<body>
    <h2>家計簿一覧</h2>
    <div><a href="create_form.php" class="btn btn-primary ml-2">新作登録</a></div>
    <p id="success_message">
    <?php
        if (isset($_SESSION['update_success_message'])) {
            print($_SESSION['update_success_message']);
            unset($_SESSION['update_success_message']);
        }
    ?>
    </p>
    <form method="get" action="">
        <span><input type="submit" name="back" value="前月"></span>
        <?php
            print($_SESSION['year_month']->format("Y年m月"));
        ?>
        <span><input type="submit" name="go" value="次月"></span>
    </form>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>日付</th><th>費目名</th><th>メモ</th><th>入金額</th><th>出金額</th><th></th>
            </tr>    
        </thead>
        <tbody>
        <?php 
        $income_total = 0;
        $expense_total = 0;
        while ($row = $stt->fetch(PDO::FETCH_ASSOC)) { 
            $income_total  += $row['入金額'];
            $expense_total += $row['出金額'];    
        ?>
            <tr>
                <td><?=e($row['日付']) ?></td>
                <td><?=e($row['費目名']) ?></td>
                <td><?=e($row['メモ']) ?></td>
                <td><?=e($row['入金額']) ?></td>
                <td><?=e($row['出金額']) ?></td>
                <td>
                    <a href="update_form.php?id=<?=e($row['id']) ?>" class="btn btn-secondary ml-2">編集</a>
                    <a href="delete_form.php?id=<?=e($row['id']) ?>" class="btn btn-danger ml-2">削除</a>
                </td>
        <?php 
        } 
        ?>
        </tbody>
        <tfoot>
            <tr class="kakeibo_total">
                <td colspan="3"></td>
                <td><?=$income_total ?></td>
                <td><?=$expense_total ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table> 
</body>
</html>