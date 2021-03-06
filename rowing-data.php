<?php
$rowing_before_2018 = 627654;
$rowing = array();
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://danube.bzz.hu/data.php?pu=1");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$content = curl_exec($ch);
curl_close($ch);

$lines = explode("\n", $content);
$c = 0;

foreach ($lines as $line) {
    $parts = explode(",", $line);
    $rowing[$parts[0]] = array($parts[2], $parts[1]);
}

$rowing_all_2018 = 0;
foreach ($rowing as $m) $rowing_all_2018 += $m[0];

$rowing_jan = 0;
foreach ($rowing as $m) $rowing_jan += $m[1];
$rowing_goal = 2801791;

$rowing_max = 0;
foreach ($rowing as $m) if ($m[0] > $rowing_max) $rowing_max = $m[0];

$data['rowing_max'] = $rowing_max;
$data['rowing_all_2018'] = $rowing_all_2018;
$data['rowing_goal'] = $rowing_goal;
$data['rowing_before_2018'] = $rowing_before_2018;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://danube.bzz.hu/map.php?pu=1&g=".calc($data['rowing_goal'], "2020-12-31")."&p=".$data['rowing_all_2018']);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$content = curl_exec($ch);
curl_close($ch);

$data['rowing_map'] = $content;
