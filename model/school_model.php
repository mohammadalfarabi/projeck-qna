<?php

require_once __DIR__ . '/../koneksi.php';


function get_all_schools() {
    global $conn;

    $query = "SELECT `school_id`, `school_name` FROM `school`";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        error_log("DB Error get_all_schools: " . mysqli_error($conn));
        return [];
    }

    $schools = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $schools[] = $row;
    }

    return $schools;
}

function school_exists($school_name) {
    global $conn;

    $school_name = mysqli_real_escape_string($conn, $school_name);

    $query = "
        SELECT `school_id` 
        FROM `school` 
        WHERE `school_name` = '$school_name'
        LIMIT 1
    ";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        error_log("DB Error school_exists: " . mysqli_error($conn));
        return false;
    }

    if ($row = mysqli_fetch_assoc($result)) {
        return $row['school_id'];
    }

    return false;
}

function add_school($school_name) {
    global $conn;

    // cek apakah sudah ada
    $existing_school_id = school_exists($school_name);

    if ($existing_school_id !== false) {
        return $existing_school_id; // tidak ubah behavior
    }

    $school_name = mysqli_real_escape_string($conn, $school_name);

    $query = "
        INSERT INTO `school` (`school_name`) 
        VALUES ('$school_name')
    ";

    if (mysqli_query($conn, $query)) {
        return mysqli_insert_id($conn);
    } else {
        error_log("DB Error add_school: " . mysqli_error($conn));
        return false;
    }
}

?>
