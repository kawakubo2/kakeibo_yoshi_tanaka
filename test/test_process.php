<?php
session_start();

$_SESSION['member_name'] = $_POST['member_name'];
$_SESSION['point'] = $_POST['point'];

print('$_SERVER["HTTP_HOST"] = ' . $_SERVER['HTTP_HOST'] . '<br>');
print('$_SERVER["PHP_SELF"] = ' . $_SERVER['PHP_SELF'] . '<br>');
print('dirnmae($_SERVER["PHP_SELF"] = ' . dirname($_SERVER['PHP_SELF']) . '<br>');
print('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/test_form.php' . '<br>');
header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/test_form.php');