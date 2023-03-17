<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>$_SERVER['HTTP_HOST']と$_SERVER['PHP_SELF']</h1>
    <form method="post" action="test_process.php">
        <div class="container">
            <label for="member_name">メンバ名: </label>
            <input type="text" name="member_name" id="member_name" value="<?=$_SESSION['member_name'] ?>" />
        </div>
        <div class="container">
            <label for="point">付与ポイント: </label>
            <input type="text" name="point" id="point" value="<?=$_SESSION['point'] ?>" />
        </div>
        <input type="submit" value="送信" />
    </form>
</body>
</html>