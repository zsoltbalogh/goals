<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://kezenfogvamozdulunk.hu/tamogatas/?p_id=4&t_id=589&c_id=586");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$content = curl_exec($ch);
curl_close($ch);

$pe = strpos($content, " Forint</strong>");
$ps = strpos($content, "<strong>", $pe - 25);

$amount = str_replace(' ', '', substr($content, $ps + 8, $pe - $ps - 8));

$journey_amount = $amount;
