<?php
include_once "config.php";
include_once "../config_local.php";

$conn = mysqli_connect($sql_server, $sql_user, $sql_password, $sql_database, $sql_port);
if (!$conn) {
  echo mysqli_connect_error();
}
?>
