<?php
require_once '../common/DbManager.php';

session_start();

$_SESSION['update_date']     = $_POST['date'];
$_SESSION['update_category'] = $_POST['category'];
$_SESSION['update_memo']     = $_POST['memo'];
$_SESSION['update_income']   = $_POST['income'];
$_SESSION['update_expense']   = $_POST['expense'];

print('<pre>');
print_r($_SESSION);
print('</pre>');

///////////////////////////////////////////////////
// 入力値検証(バリデーション機能)
///////////////////////////////////////////////////
$errors = [];
if (trim($_SESSION['update_date']) === '') {
    $errors[] = '日付は必須入力です。';
}

if ($_SESSION['update_category'] === '') {
    $errors[] = '費目は必須選択です。';
} else {
    try {
        $db = getDb();
        $sql = "SELECT id
                FROM 費目
                WHERE id = :id";
        $stt = $db->prepare($sql);
        $stt->bindValue(':id', $_SESSION['update_category']);
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

if (mb_strlen($_SESSION['update_memo']) > 255) {
    $error[] = 'メモは255文字以内で入力してください。';
}

if (mb_strlen($_SESSION['update_memo']) > 255) {
    $errors[] = 'メモは255文字以内で入力してください。'; 
}

if ($_SESSION['update_category'] !== '') {
    if ($_SESSION['update_income'] === NULL || $_SESSION['update_income'] === '') $_SESSION['update_income'] = 0;
    if ($_SESSION['update_expense'] === NULL || $_SESSION['update_expense'] === '') $_SESSION['update_expense'] = 0;
    if ($_SESSION['balance_categories'][$_SESSION['update_category']] === '入金' ) {
        if (!($_SESSION['update_income'] > 0 && $_SESSION['update_expense'] == 0)) {
            $errors[] = "{$_SESSION['category_items'][$_SESSION['update_category']]}の場合、入金額に0以上、出金額に0を入力してください。";
        }
    }
    if ($_SESSION['balance_categories'][$_SESSION['update_category']] === '出金') {
        if (!($_SESSION['update_expense'] > 0 && $_SESSION['update_income'] == 0)) {
            $errors[] = "{$_SESSION['category_items'][$_SESSION['update_category']]}の場合、出金額に0以上、入金額に0を入力してください。";
        }
    }
}

if (count($errors) > 0) {
    $_SESSION['update_errors'] = $errors;
    header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/create_form.php');
    exit(1);
}

///////////////////////////////////////////////////
// エラーがなければ登録
///////////////////////////////////////////////////
try {
    $db = getDb();
    $sql = "UPDATE 家計簿
            SET 日付 = :date, 費目id = :category, メモ = :memo, 入金額 = :income, 出金額 = :expense
            WHERE id = :id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':date',     $_SESSION['update_date']);
    $stt->bindValue(':category', $_SESSION['update_category']);
    $stt->bindValue(':memo',     $_SESSION['update_memo']);
    $stt->bindValue(':income',   $_SESSION['update_income']);
    $stt->bindValue(':expense',  $_SESSION['update_expense']);
    $stt->bindValue(':id',  $_SESSION['update_id']);
    $stt->execute();
    unset($_SESSION['update_date']);
    unset($_SESSION['update_category']);
    unset($_SESSION['update_memo']);
    unset($_SESSION['update_income']);
    unset($_SESSION['update_expense']);
    unset($_SESSION['update_id']);
    $_SESSION['update_success_message'] = '更新に成功しました。';
    header('Location: http://' . $_SERVER['HTTP_HOST']
        . dirname($_SERVER['PHP_SELF']) . '/list.php');
} catch(PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}