<?php
require '../app/config/config.php';




$con = mysql::get();
$mysqli = @new mysqli($con['host'],$con['user'], $con['password'], $con['db']);

if (mysqli_connect_errno()) {
    echo "Подключение невозможно: ".mysqli_connect_error();
}








$mysqli->close();