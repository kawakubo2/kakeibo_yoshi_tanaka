<?php
require_once '../common/DbManager.php';

session_start();

$_SESSION['insert_date']     = $_POST['date'];
$_SESSION['insert_category'] = $_POST['category'];
$_SESSION['insert_memo']     = $_POST['memo'];
$_SESSION['insert_income']   = $_POST['income'];
$_SESSION['insert_expense']   = $_POST['expense'];

print('<pre>');
print_r($_SESSION);
print('</pre>');

///////////////////////////////////////////////////
// 入力値検証(バリデーション機能)
///////////////////////////////////////////////////
$errors = [];
if (trim($_SESSION['insert_date']) === '') {
    $errors[] = '日付は必須入力です。';
}

if ($_SESSION['insert_category'] === '') {
    $errors[] = '費目は必須選択です。';
} else {
    try {
        $db = getDb();
        $sql = "SELECT id
                FROM 費目
                WHERE id = :id";
        $stt = $db->prepare($sql);
        $stt->bindValue(':id', $_SESSION['insert_category']);
        $stt->execute();
        if ($stt->fetch(PDO::FETCH_ASSOC)) {
            ;
        } else {
            $errors[] = '存在する費目を選択してください。';
        }
    } catch(PDOException $e) {
        die('エラーメッセージ: ' . $e->getMessage());
    }
}

if (mb_strlen($_SESSION['insert_memo']) > 255) {
    $error[] = 'メモは255文字以内で入力してください。';
}

if (mb_strlen($_SESSION['insert_memo']) > 255) {
    $errors[] = 'メモは255文字以内で入力してください。'; 
}

if ($_SESSION['insert_category'] !== '') {
    if ($_SESSION['insert_income'] === NULL || $_SESSION['insert_income'] === '') $_SESSION['insert_income'] = 0;
    if ($_SESSION['insert_expense'] === NULL || $_SESSION['insert_expense'] === '') $_SESSION['insert_expense'] = 0;
    if ($_SESSION['balance_categories'][$_SESSION['insert_category']] === '入金' ) {
        if (!($_SESSION['insert_income'] > 0 && $_SESSION['insert_expense'] == 0)) {
            $errors[] = "{$_SESSION['category_items'][$_SESSION['insert_category']]}の場合、入金額に0以上、出金額に0を入力してください。";
        }
    }
    if ($_SESSION['balance_categories'][$_SESSION['insert_category']] === '出金') {
        if (!($_SESSION['insert_expense'] > 0 && $_SESSION['insert_income'] == 0)) {
            $errors[] = "{$_SESSION['category_items'][$_SESSION['insert_category']]}の場合、出金額に0以上、入金額に0を入力してください。";
        }
    }
}

if (count($errors) > 0) {
    $_SESSION['insert_errors'] = $errors;
    header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/create_form.php');
    exit(1);
}

///////////////////////////////////////////////////
// エラーがなければ登録
///////////////////////////////////////////////////
try {
    $db = getDb();
    $sql = "INSERT INTO 家計簿(日付, 費目id, メモ, 入金額, 出金額)
            VALUES(:date, :category, :memo, :income, :expense)";
    $stt = $db->prepare($sql);
    $stt->bindValue(':date',     $_SESSION['insert_date']);
    $stt->bindValue(':category', $_SESSION['insert_category']);
    $stt->bindValue(':memo',     $_SESSION['insert_memo']);
    $stt->bindValue(':income',   $_SESSION['insert_income']);
    $stt->bindValue(':expense',  $_SESSION['insert_expense']);
    $stt->execute();
    unset($_SESSION['insert_date']);
    unset($_SESSION['insert_category']);
    unset($_SESSION['insert_memo']);
    unset($_SESSION['insert_income']);
    unset($_SESSION['insert_expense']);
    $_SESSION['insert_success_message'] = '登録に成功しました。';
    // 連続して登録することが多いはずなので、一覧ではなく、登録フォームへ遷移する
    header('Location: http://' . $_SERVER['HTTP_HOST']
        . dirname($_SERVER['PHP_SELF']) . '/create_form.php');
} catch(PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}