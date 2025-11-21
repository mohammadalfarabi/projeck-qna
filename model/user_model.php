<?php
// model/user_model.php

function user_find_by_email($conn, $email){
    $e = mysqli_real_escape_string($conn, $email);
    $q = "SELECT * FROM user WHERE email='$e' LIMIT 1";
    $r = mysqli_query($conn, $q);
    return mysqli_fetch_assoc($r);
}

/**
 * CREATE USER — FIXED
 * Now returns the NEW USER ID (mysqli_insert_id),
 * preventing foreign key errors in notifications.
 */
function user_create($conn, $school_id, $name, $email, $password, $role='student'){
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $password_h = password_hash($password, PASSWORD_DEFAULT);

    $q = "INSERT INTO user (school_id, name, email, password, role, points)
          VALUES ($school_id, '$name', '$email', '$password_h', '$role', 0)";

    if(mysqli_query($conn, $q)){
        return mysqli_insert_id($conn); // ← FIX UTAMA
    }

    return 0; // gagal
}

function user_get_by_id($conn, $id){
    $id = (int)$id;
    $q = "SELECT u.*, s.school_name
          FROM user u
          LEFT JOIN school s ON u.school_id = s.school_id
          WHERE u.user_id = $id";
    $r = mysqli_query($conn, $q);
    return mysqli_fetch_assoc($r);
}

function user_update_profile($conn, $user_id, $name, $email, $school_id=null){
    $user_id = (int)$user_id;
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);

    $update_school = '';
    if ($school_id !== null) {
        $update_school = ", school_id = ".(int)$school_id;
    }

    $q = "UPDATE user SET name='$name', email='$email' $update_school WHERE user_id=$user_id";
    return mysqli_query($conn, $q);
}

function user_update_password($conn, $user_id, $new_password){
    $user_id = (int)$user_id;
    $pw = password_hash($new_password, PASSWORD_DEFAULT);
    return mysqli_query($conn, "UPDATE user SET password='$pw' WHERE user_id=$user_id");
}

function user_set_photo($conn, $user_id, $filename){
    $user_id = (int)$user_id;
    $filename = mysqli_real_escape_string($conn, $filename);

    $now = date('Y-m-d H:i:s');
    mysqli_query($conn, "INSERT INTO profile_photos (user_id, photo, uploaded_at)
                         VALUES ($user_id, '$filename', '$now')");
    return true;
}

function user_get_photo($conn, $user_id){
    $user_id = (int)$user_id;
    $r = mysqli_query($conn, "SELECT photo FROM profile_photos WHERE user_id=$user_id ORDER BY uploaded_at DESC LIMIT 1");
    if ($row = mysqli_fetch_assoc($r)) return $row['photo'];
    return null;
}

function user_add_points($conn, $user_id, $points){
    $user_id = (int)$user_id;
    $points = (int)$points;
    return mysqli_query($conn, "UPDATE user SET points = points + $points WHERE user_id=$user_id");
}

function user_get_points($conn, $user_id){
    $user_id = (int)$user_id;
    $r = mysqli_query($conn, "SELECT points FROM user WHERE user_id=$user_id LIMIT 1");
    $row = mysqli_fetch_assoc($r);
    return $row ? (int)$row['points'] : 0;
}

function user_list_all($conn){
    return mysqli_query($conn, "SELECT * FROM user");
}

function user_delete($conn, $user_id){
    $user_id = (int)$user_id;
    return mysqli_query($conn, "DELETE FROM user WHERE user_id=$user_id");
}

function user_change_role($conn, $user_id, $role){
    $user_id = (int)$user_id;
    $role = mysqli_real_escape_string($conn, $role);
    return mysqli_query($conn, "UPDATE user SET role='$role' WHERE user_id=$user_id");
}

function leaderboard_by_school($conn, $school_id, $limit=10){
    $school_id = (int)$school_id;
    $limit = (int)$limit;
    $q = "SELECT user_id, name, points
          FROM user
          WHERE school_id=$school_id
          ORDER BY points DESC
          LIMIT $limit";
    return mysqli_query($conn, $q);
}

function user_get_photo_url($conn, $user_id){
    $user_id = (int)$user_id;
    $r = mysqli_query($conn, "SELECT photo FROM profile_photos WHERE user_id=$user_id ORDER BY uploaded_at DESC LIMIT 1");
    if ($row = mysqli_fetch_assoc($r)){
        return 'uploads/' . $row['photo']; // lokasi simpan
    }
    return 'assets/img/default.png'; // default avatar
}

?>
