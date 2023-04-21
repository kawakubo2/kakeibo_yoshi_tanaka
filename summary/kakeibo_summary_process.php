<?php
require_once '../common/DbManager.php';

session_start();

$_SESSION['search_year'] = $_GET['search_year'];
$_SESSION['start_month'] = $_GET['start_month'];
$_SESSION['end_month'] = $_GET['end_month'];

// 入力値検証
$errors = [];
if (trim($_SESSION['search_year']) === '') {
    $errors[] = '年は必須選択です';
}

if ($_SESSION['start_month'] > $_SESSION['end_month']) {
    $errors[] = '開始月は終了月より前の月を選択してください。';
}

if (count($errors) > 0) {
    $_SESSION['summary_errors'] = $errors;
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/summary/kakeibo_summary_form.php');
    exit();
}

try {
    $db = getDb();
    $sql = "SELECT H.費目名, MONTH(K.日付) AS 月, SUM(K.入金額 + K.出金額) AS 月毎入出金額合計
            FROM 家計簿 AS K
                INNER JOIN 費目 AS H
                    ON K.費目id = H.id
            WHERE YEAR(K.日付) = :year
            GROUP BY H.費目名, MONTH(K.日付)
            ORDER BY H.費目名, MONTH(K.日付)";
    $stt = $db->prepare($sql);
    $stt->bindValue(':year', $_SESSION['search_year']);
    $stt->execute();
    $result = [];
    /*
        [
            '食費' => [1 => 57891, 2 => 71428, 3 => 67123, ... 12 => 68517],
            '給料' => [1 => 189000, 2 => 198000, 3 => 201000, ... 12 => 192800],
            ...
            '通信費' => [1 => 23189, 2 => 18775, 3 => 25918, ... 12 => 20791]
        ]
    */
    while ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($result[$row['費目名']])) {
            $result[$row['費目名']] = [];
        }
        $result[$row['費目名']][$row['月']] = $row['月毎入出金額合計'];
    }
    $_SESSION['summary_result'] = $result;
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/summary/kakeibo_summary_form.php');
    exit();
    /*
    print('<pre>');
    print_r($result);
    print('</pre>');
    */
} catch (PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}
