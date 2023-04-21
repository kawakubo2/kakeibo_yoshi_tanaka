<?php
require_once '../common/DbManager.php';
require_once '../common/Encode.php';

session_start();

if (!isset($_SESSION['summary_years']) || !isset($_SESSION['income_expense_kubun'])) {
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

        $income_expense_kubun = []; // キー:費目名 値:入出金区分
        $category_sql = "SELECT 費目名, 入出金区分
                         FROM 費目
                         ORDER BY id";
        $category_stt = $db->prepare($category_sql);
        $category_stt->execute();
        while ($row = $category_stt->fetch(PDO::FETCH_ASSOC)) {
            $income_expense_kubun[$row['費目名']] = $row['入出金区分'];
        }
        $_SESSION['income_expense_kubun'] = $income_expense_kubun;
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
        <div>
        <label>
            検索年: 
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
        <label>
            &nbsp;&nbsp;検索月: 
            <select name="start_month">
            <?php for ($month = 1; $month <= 12; $month++) { ?>
                    <option value="<?=$month ?>"><?=$month ?></option>
            <?php } ?>
            </select>
            月 ～ 
            <select name="end_month">
            <?php for ($month = 1; $month <= 12; $month++) { 
                    $prop = ($month === 12) ? 'selected': '';    
            ?>
                    <option value="<?=$month ?>" <?=$prop ?> ><?=$month ?></option>
            <?php } ?>
            </select>
            月
        </label>
        </div>
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
            $income_totals = [];
            $expense_totals = [];
            for ($month = 1; $month <= 12; $month++) {
                $income_totals[$month] = 0;
                $expense_totals[$month] = 0;
            }
        ?>
        <?php foreach ($_SESSION['summary_result'] as $category_name => $summary_data) { ?>
            <tr>
                <th><?=e($category_name) ?></th>
                <?php $category_total = 0; ?>
                <?php 
                    for ($month = 1; $month <= 12; $month++) { 
                        if ($_SESSION['income_expense_kubun'][$category_name] === '出金') {
                            $expense_totals[$month] += $summary_data[$month];
                        } else {
                            $income_totals[$month] += $summary_data[$month];
                        }
                        $category_total += $summary_data[$month];
                ?>
                        <td><?=e($summary_data[$month]) ?></td>
                <?php 
                    } 
                ?>
                <td><?=e($category_total) ?></td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <th>入金額合計</th>
                <?php
                    $income_year_total =  0;
                    for ($month = 1; $month <= 12; $month++) {
                        $income_year_total += $income_totals[$month];
                ?>
                    <td><?=$income_totals[$month] ?></td>
                <?php
                    }
                ?>
                    <td><?=$income_year_total ?></td>
            </tr>
            <tr>
                <th>出金額合計</th>
                <?php
                    $expense_year_total =  0;
                    for ($month = 1; $month <= 12; $month++) {
                        $expense_year_total += $expense_totals[$month];
                ?>
                    <td><?=$expense_totals[$month] ?></td>
                <?php
                    }
                ?>
                    <td><?=$expense_year_total ?></td>
            </tr>
            <tr>
                <th>収支</th>
                <?php
                    $grand_balance = 0;
                    for ($month = 1; $month <= 12; $month++) {
                        $difference = ($income_totals[$month] - $expense_totals[$month]);
                        $grand_balance += $difference; 
                ?>
                        <td><?=$difference ?></td>
                <?php
                    }
                ?>
                    <td><?=$grand_balance ?></td>
            </tr>
        </tfoot>
    </table>
    <?php } ?>
</body>
</html>