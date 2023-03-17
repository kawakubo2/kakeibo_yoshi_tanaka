<?php
require_once 'Person.php';

print(Person::$SPECIES . '<br>');
$p1 = new Person('太郎', '山田', 171.5, 58.5);
$p2 = new Person('花子', '横山', 158.3, 52.0);

print($p1->getName() . 'さんのBMI値は' . $p1->bmi() . 'です。<br />');
print($p2->getName() . 'さんのBMI値は' . $p2->bmi() . 'です。<br />');