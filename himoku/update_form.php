<?php
require_once '../common/DbManager.php';
require_once '../common/Encode.php';

session_start();

if (isset($_GET['id'])) {
    try {
        $db = getDb();
        $sql = "SELECT id, 費目名, 入出金区分, メモ
                FROM 費目
                WHERE id = :id";
        $stt = $db->prepare($sql);
        $stt->bindValue(':id', $_GET['id']);
        $stt->execute();
        if ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
            $_SESSION['update_id'] = $row['id'];
            $_SESSION['update_category_name'] = $row['費目名'];
            $_SESSION['update_old_category_name'] = $row['費目名'];
            $_SESSION['update_balance_category'] = $row['入出金区分'];
            $_SESSION['update_memo'] = $row['メモ'];
        } else {
            die('エラーメッセージ: ' . '該当する費目が存在しません。');
        }
    } catch(PDOException $e) {
        die('エラーメッセージ:' . $e->getMessage());  
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
    <title>費目の編集 | 山田家の家計簿</title>
</head>
<body>
    <h2>費目の編集</h2>
    <div><a href="list.php" class="btn btn-success ml-2">費目一覧へ戻る</a></div>
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
            <label for="category_name">費目名: </label><br />
            <!-- update_xxxとすべきところ input_xxxとしていた -->
            <input type="text" name="category_name" id="category_name" size="30" 
                value="<?=isset($_SESSION['update_category_name']) ? $_SESSION['update_category_name'] : '' ?>" />
        </div>
        <?php
            $balance_categories = ['入金', '出金'];
        ?>
        <div class="container">
            <label for="balance_category">入出金区分</label><br />
            <select name="balance_category" id="balance_category">
                <option value="">選択</option>
            <?php
               foreach($balance_categories as $b) {
                    if (isset($_SESSION['update_balance_category'])) {
                        if ($_SESSION['update_balance_category'] == $b) {
                            $prop = 'selected';
                        } else {
                            $prop = '';
                        }
                    }
                ?>
                    <option value="<?=$b ?>" <?=$prop ?> ><?=$b ?></option>
            <?php
               } 
            ?>
            </select>
        </div>
        <div class="container">
            <label for="memo">メモ: </label><br>
            <!-- update_xxxとすべきところ input_xxxとしていた -->
            <textarea id="memo" name="memo"
                rows="5" cols="40"><?=isset($_SESSION['update_memo']) ? $_SESSION['update_memo'] : '' ?></textarea>
        </div>
        <input type="submit" value="更新">
    </form>
</body>
</html>