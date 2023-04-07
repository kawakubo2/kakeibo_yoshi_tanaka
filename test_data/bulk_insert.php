<?php
$himokus = [
		"給料",
		"食費",
		"教養娯楽費",
		"交際費",
		"水道光熱費",
        "通信費",
		"居住費",
];

$himoku_freq = [
	1 => 1,
	2 => 100,
	3 => 3,
	4 => 5,
	5 => 1,
	6 => 1,
    7 => 1
];

$himoku_price = [
	1 => 280000,
	2 => 100,
	3 => 2000,
	4 => 3000,
	5 => 12000,
    6 => 6000,
	7 => 85000
];

$himoku_range = [
		1 => 130000,
		2 => 3000,
		3 => 10000,
		4 => 20000,
		5 => 5000,
        6 => 2000,
		7 => 0
];

require_once '../common/DbManager.php';

try {
	$db = getDb();
	$db->beginTransaction();
	$sql = "INSERT INTO 家計簿(日付,費目id,入金額,出金額)
			VALUES(:hiduke, :himoku, :nyukin, :shukkin)";
	$stt = $db->prepare($sql);
	for ($year = 2017; $year <= 2022; $year++) {
		for ($month = 1; $month <= 12; $month++) {
			foreach ($himoku_freq as $himoku => $freq) {
				for ($i = 0; $i < $freq; $i++) {
					$price = $himoku_price[$himoku] + mt_rand(0, $himoku_range[$himoku]);
					$hiduke = date('Y-m-d', mktime(0, 0, 0, $month, mt_rand(1, 28), $year));
					$stt->bindValue(':hiduke', $hiduke);
					$stt->bindValue(':himoku', $himoku);
					if ($himoku !== 1) {
						$stt->bindValue(':nyukin', 0);
						$stt->bindValue(':shukkin', $price);
					} else {
						$stt->bindValue(':nyukin', $price);
						$stt->bindValue(':shukkin', 0);
					}
					$stt->execute();
				}
			}
		}
	}
	$db->commit();
} catch (PDOException $e) {
	$db->rollBack();
	die('エラーメッセージ:' . $e->getMessage());
}