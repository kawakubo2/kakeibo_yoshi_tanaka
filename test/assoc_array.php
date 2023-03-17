<?php
$members = [];
$members[1001] = '山田太郎';
$members[1002] = '横山花子';
$members[1003] = '田中宏';
$members[1004] = '山本久美子';

print('<pre>');
print_r($members);
print('</pre>');

require_once '../common/DbManager.php';
session_start();

$items = [];
try {
    $db = getDb();
    $sql = "SELECT id, 費目名, 入出金区分
            FROM 費目
            ORDER BY id";
    $stt = $db->prepare($sql);
    $stt->execute();
    while($row = $stt->fetch(PDO::FETCH_ASSOC)) { // ['id' => 1, '費目名' => '食費', '入出金区分' => '出金']
        print('<pre>');
        print_r($row);
        print('</pre>');
        $items[$row['id']] = $row['費目名'];
    }
    print('<pre>');
    print_r($items);
    print('</pre>');
    $_SESSION['items'] = $items;
} catch(PDOException $e) {
    die('エラーメッセージ' . $e->getMessage());
}