<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "ing_qna";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
