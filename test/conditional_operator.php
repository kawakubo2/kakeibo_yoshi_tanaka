<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>条件演算子</h1> 
    <?php
        $score = 77;

        // print($score >= 70 ? "合格": "不合格");
        if ($score >= 70) {
            print("合格");
        } else {
            print("不合格");
        }

    ?>
</body>
</html>