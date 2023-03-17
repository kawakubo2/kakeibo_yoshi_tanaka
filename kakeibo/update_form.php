<?php
require_once '../common/DbManager.php';
require_once '../common/Encode.php';

session_start();

if (!isset($_SESSION['balance_categories']) || !isset($_SESSION['category_items'])) {
    try {
        $balance_categories = [];
        $category_items = [];
        $db = getDb();
        $sql = "SELECT id, 費目名, 入出金区分
                FROM 費目
                ORDER BY id";
        $stt = $db->prepare($sql);
        $stt->execute();
        while ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
            $balance_categories[$row['id']] = $row['入出金区分'];
            $category_items[$row['id']] = $row['費目名'];
        }
        $_SESSION['balance_categories'] = $balance_categories;
        $_SESSION['category_items'] = $category_items;
        $db = NULL;
    } catch(PDOException $e) {
        die('エラーメッセージ: ' . $e->getMessage());
    }
}
if (isset($_GET['id'])) {
    try {
        $db = getDb();
        $sql = "SELECT id, 日付, 費目id, メモ, 入金額, 出金額
                FROM 家計簿
                WHERE id = :id";
        $stt = $db->prepare($sql);
        $stt->bindValue(':id', $_GET['id']);
        $stt->execute();
        if ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
            $_SESSION['update_id']       = $row['id'];
            $_SESSION['update_date']     = $row['日付'];
            $_SESSION['update_category'] = $row['費目id'];
            $_SESSION['update_memo']     = $row['メモ'];
            $_SESSION['update_income']   = $row['入金額']; 
            $_SESSION['update_expense']  = $row['出金額']; 
        }
    } catch(PDOException $e) {
        die('エラーメッセージ: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <title>家計簿の新規登録 | 山田家の家計簿</title>
</head>
<body>
    <h2>家計簿の新規登録</h2>
    <div><a href="list.php" class="btn btn-success ml-2">家計簿一覧へ戻る</a></div>
    <ul id="error_summary">
        <?php
            if (isset($_SESSION['update_errors'])) {
                foreach($_SESSION['update_errors'] as $error) {
                    print("<li>{$error}</li>");
                }
                unset($_SESSION['update_errors']);
            }
        ?>
    </ul>
    <form method="post" action="update_process.php">
        <div class="container">
            <label for="date">日付:</label><br />
            <input type="date" name="date" id="date" value="<?=e($_SESSION['update_date']) ?>" />
        </div>
        <div class="container">
            <label for="category">費目: </label><br />
            <select id="category" name="category">
                <option value="">--選択--</option>
            <?php foreach($_SESSION['category_items'] as $category_id => $category_name) { 
                if ($category_id == $_SESSION['update_category']) {
                    $prop = 'selected';
                } else {
                    $prop = '';
                }
            ?>
                <option value="<?=e($category_id) ?>" <?=$prop ?> ><?=e($category_name) ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="container">
            <label for="memo">メモ: </label><br />
            <textarea id="memo" name="memo"
                rows="5" cols="40"><?=e($_SESSION['update_memo']) ?></textarea>
            </select>
        </div>
        <div class="container">
            <label for="income">入金額: </label><br />
            <input type="number" name="income" id="income" value="<?=e($_SESSION['update_income']) ?>" />
        </div>
        <div class="container">
            <label for="income">出金額: </label><br />
            <input type="number" name="expense" id="expense" value="<?=e($_SESSION['update_expense']) ?>" />
        </div>
        <input type="submit" value="更新" />
    </form>
</body>
</html>