<?php
require_once 'model/user_model.php';
require_once 'model/profile_model.php';
require_once 'model/notify_model.php';


// -------------------------------------------------------
// SHOW REGISTER PAGE
// -------------------------------------------------------
function show_register(){
    require 'view/register.php';
}



// -------------------------------------------------------
// HANDLE REGISTER
// -------------------------------------------------------
function do_register(){
    global $conn;

    $name      = trim($_POST['name']);
    $email     = trim($_POST['email']);
    $password  = $_POST['password'];
    $school_id = (int)$_POST['school_id'];

    if ($name == "" || $email == "" || $password == "") {
        $_SESSION['error'] = "Semua field wajib diisi.";
        header("Location: index.php?page=register");
        exit;
    }

    // Cek email sudah terdaftar
    if (user_find_by_email($conn, $email)) {
        $_SESSION['error'] = "Email sudah digunakan.";
        header("Location: index.php?page=register");
        exit;
    }

    // Register user baru
    $new_id = user_create($conn, $school_id, $name, $email, $password);

    // Notifikasi selamat datang
    notify_create($conn, $new_id, "Selamat datang di platform QnA!");

    $_SESSION['success'] = "Registrasi berhasil. Silakan login!";
    header("Location: index.php?page=login");
    exit;
}



// -------------------------------------------------------
// SHOW LOGIN PAGE
// -------------------------------------------------------
function show_login(){
    require 'view/login.php';
}



// -------------------------------------------------------
// HANDLE LOGIN
// -------------------------------------------------------
function do_login(){
    global $conn;

    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $user = user_find_by_email($conn, $email);

    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['error'] = "Email atau password salah!";
        header("Location: index.php?page=login");
        exit;
    }

    // Simpan session
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['role']    = $user['role'];

    // Log masuk aktivitas
    mysqli_query($conn, "INSERT INTO activity_log (user_id, action, detail, created_at)
                         VALUES ({$user['user_id']}, 'login', 'user login', NOW())");

    header("Location: index.php?page=dashboard");
}



// -------------------------------------------------------
// LOGOUT
// -------------------------------------------------------
function do_logout(){
    session_destroy();
    header("Location: index.php?page=login");
    exit;
}



// -------------------------------------------------------
// SHOW PROFILE PAGE
// -------------------------------------------------------
function show_profile(){
    global $conn;

    $user_id = $_SESSION['user_id'];

    $user  = user_get_by_id($conn, $user_id);
    $photo = user_get_photo($conn, $user_id);

    require 'view/profile.php';
}



// -------------------------------------------------------
// HANDLE PROFILE UPDATE
// -------------------------------------------------------
function do_update_profile(){
    global $conn;

    $user_id = $_SESSION['user_id'];
    $name    = $_POST['name'];
    $email   = $_POST['email'];
    $school  = (int)$_POST['school_id'];

    user_update_profile($conn, $user_id, $name, $email, $school);

    notify_create($conn, $user_id, "Profil Anda berhasil diperbarui.");
    mysqli_query($conn, "INSERT INTO activity_log (user_id,action,detail,created_at)
                         VALUES ($user_id,'profile_update','updated profile',NOW())");

    header("Location: index.php?page=profile");
}



// -------------------------------------------------------
// CHANGE PASSWORD
// -------------------------------------------------------
function do_change_password(){
    global $conn;

    $user_id = $_SESSION['user_id'];

    $old = $_POST['old_password'];
    $new = $_POST['new_password'];

    $user = user_get_by_id($conn, $user_id);

    if (!password_verify($old, $user['password'])) {
        $_SESSION['error'] = "Password lama salah!";
        header("Location: index.php?page=profile");
        exit;
    }

    user_update_password($conn, $user_id, $new);

    notify_create($conn, $user_id, "Password berhasil diganti.");
    mysqli_query($conn, "INSERT INTO activity_log (user_id,action,detail,created_at)
                         VALUES ($user_id,'password_change','changed password',NOW())");

    header("Location: index.php?page=profile");
}



// -------------------------------------------------------
// UPLOAD PHOTO PROFILE
// -------------------------------------------------------
function do_upload_photo(){
    global $conn;

    $user_id = $_SESSION['user_id'];

    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] != 0) {
        $_SESSION['error'] = "Upload gagal!";
        header("Location: index.php?page=profile");
        exit;
    }

    $file = $_FILES['photo'];
    $result = profile_upload_photo($conn, $user_id, $file['tmp_name'], $file['name']);

    if ($result) {
        notify_create($conn, $user_id, "Foto profil telah diperbarui!");
    }

    header("Location: index.php?page=profile");
}

?>
