<?php

require_once __DIR__ . '/../koneksi.php';

function get_users_by_school($school_id) {
    global $conn;
    $school_id = mysqli_real_escape_string($conn, $school_id);
    $query = "SELECT u.*, s.school_name as school_name FROM user u JOIN school s ON u.school_id = s.school_id WHERE u.school_id = '$school_id'";
    $result = mysqli_query($conn, $query);

    $users = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }
    return $users;
}

function get_all_users() {
    global $conn;
    $query = "SELECT u.*, s.school_name as school_name FROM user u JOIN school s ON u.school_id = s.school_id";
    $result = mysqli_query($conn, $query);

    $users = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }
    return $users;
}

// Added function: get_user_by_email
function get_user_by_email($email) {
    global $conn;
    $email = mysqli_real_escape_string($conn, $email);
    $query = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

// Added function: create_user with role
function create_user($name, $email, $password, $school_id, $role) {
    global $conn;
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);
    $school_id = mysqli_real_escape_string($conn, $school_id);
    $role = mysqli_real_escape_string($conn, $role);

    $query = "INSERT INTO user (name, email, password, school_id, role) VALUES ('$name', '$email', '$password', '$school_id', '$role')";
    return mysqli_query($conn, $query);
}

// Added function: get_user_by_id
function get_user_by_id($user_id) {
    global $conn;
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $query = "SELECT u.*, s.school_name as school_name FROM user u JOIN school s ON u.school_id = s.school_id WHERE u.user_id = '$user_id'";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

// Added function: update_user
function update_user($user_id, $name, $email, $role, $school_id) {
    global $conn;
    $user_id = (int)$user_id;
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $role = mysqli_real_escape_string($conn, $role);
    $school_id = (int)$school_id;

    $query = "UPDATE user SET name = '$name', email = '$email', role = '$role', school_id = $school_id WHERE user_id = $user_id";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        return "Database error: " . mysqli_error($conn);
    }
    return true;
}

// Added function: delete_user
function delete_user($user_id) {
    global $conn;
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $query = "DELETE FROM user WHERE user_id = '$user_id'";
    return mysqli_query($conn, $query);
}
?>