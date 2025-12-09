<?php
$conn = mysqli_connect("localhost", "root", "", "ing_qna", null, "/opt/lampp/var/mysql/mysql.sock");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}