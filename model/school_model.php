<?php

require_once __DIR__ . '/../koneksi.php';

function get_all_schools() {
    global $conn;
    $query = "SELECT school_id, school_name FROM school";
    $result = mysqli_query($conn, $query);

    $schools = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $schools[] = $row;
        }
    }
    return $schools;
}

function school_exists($school_name) {
    global $conn;
    $school_name = mysqli_real_escape_string($conn, $school_name);
    $query = "SELECT school_id FROM school WHERE school_name = '$school_name' LIMIT 1";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['school_id'];
    }
    return false;
}

function add_school($school_name) {
    global $conn;
    $existing_school_id = school_exists($school_name);
    if ($existing_school_id !== false) {
        // School already exists, return existing ID
        return $existing_school_id;
    }
    $school_name = mysqli_real_escape_string($conn, $school_name);
    $query = "INSERT INTO school (school_name) VALUES ('$school_name')";
    if (mysqli_query($conn, $query)) {
        return mysqli_insert_id($conn);
    } else {
        return false;
    }
}
?>