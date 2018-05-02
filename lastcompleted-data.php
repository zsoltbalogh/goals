<?php
if ($_GET['update']) {
    $data['lastcompleted_' . $_GET['update']] = date("Y-m-d");
}
