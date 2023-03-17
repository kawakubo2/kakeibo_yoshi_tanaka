<?php
require_once '../common/DbManager.php';

if (isset($_POST['cancel'])) {
    header('Location: http://' . $_SERVER['HTTP_HOST']
        . dirname($_SERVER['PHP_SELF']) . '/list.php');
    exit(1);
}
if (isset($_POST['delete'])) {
    try {
        $db = getDb();
        $sql = "DELETE FROM 費目
                WHERE id = :id";
        $stt = $db->prepare($sql);
        $stt->bindValue(':id', $_POST['id']);
        $stt->execute();
        
        header('Location: http://' . $_SERVER['HTTP_HOST']
            . dirname($_SERVER['PHP_SELF']) . '/list.php');

    } catch(PDOException $e) {
        die('エラーメッセージ: ' . $e->getMessage());
    }
}