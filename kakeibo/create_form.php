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
    <p id="success_message">
    <?php
        if (isset($_SESSION['insert_success_message'])) {
            print($_SESSION['insert_success_message']);
            unset($_SESSION['insert_success_message']);
        }
    ?>
    </p>
    <ul id="error_summary">
        <?php
            if (isset($_SESSION['insert_errors'])) {
                foreach($_SESSION['insert_errors'] as $error) {
                    print("<li>{$error}</li>");
                }
                unset($_SESSION['insert_errors']);
            }
        ?>
    </ul>
    <form method="post" action="create_process.php">
        <div class="container">
            <label for="date">日付:</label><br />
            <input type="date" name="date" id="date" value="<?=e($_SESSION['insert_date']) ?>" />
        </div>
        <div class="container">
            <label for="category">費目: </label><br />
            <select id="category" name="category">
                <option value="">--選択--</option>
            <?php foreach($_SESSION['category_items'] as $category_id => $category_name) { 
                if ($category_id == $_SESSION['insert_category']) {
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
                rows="5" cols="40"><?=e($_SESSION['insert_memo']) ?></textarea>
            </select>
        </div>
        <div class="container">
            <label for="income">入金額: </label><br />
            <input type="number" name="income" id="income" value="<?=e($_SESSION['insert_income']) ?>" />
        </div>
        <div class="container">
            <label for="income">出金額: </label><br />
            <input type="number" name="expense" id="expense" value="<?=e($_SESSION['insert_expense']) ?>" />
        </div>
        <input type="submit" value="登録" />
    </form>
</body>
</html>