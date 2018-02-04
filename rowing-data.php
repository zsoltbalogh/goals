<?php
$rowing_before_2018 = 627654;
$rowing = array(
"2018-01-02" => array(8126, 40),
"2018-01-04" => array(6132, 30),
"2018-01-05" => array(10015, 48+19/60),
"2018-01-07" => array(12029, 60),
"2018-01-13" => array(8355, 40),
"2018-01-26" => array(8112, 40),
"2018-01-28" => array(8010, 40),
"2018-01-30" => array(8398, 40),
"2018-02-03" => array(21097, 104)
);

$rowing_all_2018 = 0;
foreach ($rowing as $m) $rowing_all_2018 += $m[0];

$rowing_jan = 0;
foreach ($rowing as $m) $rowing_jan += $m[1];
$rowing_goal = 2801791;

$rowing_max = 0;
foreach ($rowing as $m) if ($m[0] > $rowing_max) $rowing_max = $m[0];
?>