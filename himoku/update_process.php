<?php
require_once '../common/DbManager.php';

session_start();

$_SESSION['update_category_name']    = $_POST['category_name'];
$_SESSION['update_balance_category'] = $_POST['balance_category'];
$_SESSION['update_memo']             = $_POST['memo'];

///////////////////////////////////////////////////
// 入力値検証(バリデーション機能)
///////////////////////////////////////////////////
$errors = [];
if (trim($_SESSION['update_category_name']) === '') {
    $errors[] = '費目名は必須です。';
} else {
    if ($_SESSION['update_old_category_name'] !== $_SESSION['update_category_name']) {
        try {
            $db = getDb();
            $sql = "SELECT * FROM 費目
                    WHERE 費目名 = :category_name";
            $stt = $db->prepare($sql);
            $stt->bindValue(':category_name', $_SESSION['update_category_name']);
            $stt->execute();
            if ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
                $errors[] = "{$_SESSION['update_category_name']}は既に存在します。";
            }
            $db = NULL;
        } catch (PDOException $e) {
            die("エラーメッセージ: " . $e->getMessage());
        }
    }
}

if ($_SESSION['update_balance_category'] === '') {
    $errors[] = '入出力区分は必須選択です。';
}

if (mb_strlen($_SESSION['update_memo']) > 255) {
    $errors[] = 'メモは255文字以内で入力してください。'; 
}

if (count($errors) > 0) {
    $_SESSION['update_errors'] = $errors;
    header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/update_form.php');
    exit(1);
}

///////////////////////////////////////////////////
// エラーがなければ登録
///////////////////////////////////////////////////
try {
    $db = getDb();
    $sql = "UPDATE 費目
            SET 費目名 = :category_name, 入出金区分 = :balance_category, メモ = :memo
            WHERE id = :id"; 
    $stt = $db->prepare($sql);
    $stt->bindValue(':category_name', $_SESSION['update_category_name']);
    $stt->bindValue(':balance_category', $_SESSION['update_balance_category']);
    $stt->bindValue(':memo', $_SESSION['update_memo']);
    $stt->bindValue(':id', $_SESSION['update_id']);
    $stt->execute();
    unset($_SESSION['update_category_name']);
    unset($_SESSION['update_old_category_name']);
    unset($_SESSION['update_balance_category']);
    unset($_SESSION['update_memo']);
    unset($_SESSION['update_id']);
    header('Location: http://' . $_SERVER['HTTP_HOST']
        . dirname($_SERVER['PHP_SELF']) . '/list.php');
} catch(PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}