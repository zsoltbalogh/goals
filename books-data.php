<?php


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.goodreads.com/user/show/57811279-zsolt-balogh");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$content = curl_exec($ch);
curl_close($ch);

$ps = strpos($content, "Zsolt Balogh has read") + 22;
$pe = strpos($content, " of ", $ps);

$data['books_read_2018'] = str_replace(' ', '', substr($content, $ps, $pe - $ps));
