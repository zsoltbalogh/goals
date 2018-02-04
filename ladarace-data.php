<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://gc.bzz.hu/api.php");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$content = curl_exec($ch);
curl_close($ch);

$lines = explode("\n", $content);
$c = 0;

foreach ($lines as $line) {
    $parts = explode(",", $line);
    $c++;
    if ($c == 1) {
        $ladarace_first = $parts[3];
    }

    if ($parts[2] == "bzz") {
        $ladarace_standing = $c;
        $ladarace_found = $parts[3];
    }
}
