<?php
require_once '../common/DbManager.php';
require_once '../common/Encode.php';

session_start();

if (!isset($_SESSION['summary_years'])) {
    try {
        $db = getDb();
        $sql = "SELECT DISTINCT year(日付) AS 年
                FROM 家計簿
                ORDER BY 年 DESC";
        $stt = $db->prepare($sql);
        $stt->execute();
        $years = [];
        while ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
            $years[] = $row['年'];
        }
        $_SESSION['summary_years'] = $years;
    } catch (PDOException $e) {
        die('エラーメッセージ: ' . $e->getMessage());
    }
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
    <h2>家計簿集計</h2>
    <form method="get" action="kakeibo_summary_process.php">
        <label>
            <select id="search_year" name="search_year">
                <option value=""></option>
        <?php
            foreach($_SESSION['summary_years'] as $year) {
                if ($year == $_SESSION['search_year']) {
                    $prop = 'selected';
                } else {
                    $prop = '';
                }
            ?>
            <option value="<?=e($year) ?>" <?=$prop ?> ><?=e($year) ?></option>    
        <?php
            }
        ?>
            </select>
        </label>
        <input type="submit" value="集計" />
    </form>
    <hr>
    <?php if (isset($_SESSION['summary_result'])) { ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>費目</th>
                <?php for ($month = 1; $month <= 12; $month++) { ?>
                    <th><?=$month ?>月</th>
                <?php } ?>
                <th>合計</th>
            </tr>
        </thead>
        <tbody>
        <?php


        ?>
        </tbody>
    </table>
    <?php } ?>
</body>
</html>