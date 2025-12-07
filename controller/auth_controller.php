<?php
session_start();
require_once '../koneksi.php';
require_once '../model/user_model.php';
require_once '../model/school_model.php';

function register() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $school_id = $_POST['school_id'];
        $role = $_POST['role'] ?? '';
        $new_school_name = trim($_POST['new_school_name'] ?? '');

        // Validasi
        if (empty($name) || empty($email) || empty($password) || (empty($school_id) && empty($new_school_name)) || empty($role)) {
            $_SESSION['error'] = "Semua field harus diisi";
            header('Location: ../controller/auth_controller.php?action=register');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Email tidak valid";
            header('Location: ../controller/auth_controller.php?action=register');
            exit;
        }

        // Cek apakah email sudah terdaftar
        $existing_user = get_user_by_email($email);
        if ($existing_user) {
            $_SESSION['error'] = "Email sudah terdaftar";
            header('Location: ../controller/auth_controller.php?action=register');
            exit;
        }

        // Jika user memasukkan nama sekolah baru, tambahkan ke database
        if (!empty($new_school_name)) {
            $new_school_id = add_school($new_school_name);
            if ($new_school_id === false) {
                $_SESSION['error'] = "Gagal menambahkan sekolah baru";
                header('Location: ../controller/auth_controller.php?action=register');
                exit;
            }
            $school_id = $new_school_id;
        }

        // Buat user baru tanpa password hashing (plain text)
        if (create_user($name, $email, $password, $school_id, $role)) {
            $_SESSION['success'] = "Registrasi berhasil. Silakan login.";
            header('Location: ../controller/auth_controller.php?action=login');
            exit;
        } else {
            $_SESSION['error'] = "Registrasi gagal";
            header('Location: ../controller/auth_controller.php?action=register');
            exit;
        }
    }
}

function login() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $selected_role = $_POST['role'];

        // Since emails can be duplicate, find user by email and password
        global $conn;
        $email = mysqli_real_escape_string($conn, $email);
        $password = mysqli_real_escape_string($conn, $password);

        $query = "SELECT u.*, s.school_name FROM user u JOIN school s ON u.school_id = s.school_id WHERE u.email = '$email' AND u.password = '$password'";
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            // Verify selected role matches user's actual role
            if ($selected_role !== $user['role']) {
                $_SESSION['error'] = "Role yang dipilih tidak sesuai dengan akun";
                header('Location: ../controller/auth_controller.php?action=login');
                exit;
            }

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['school_id'] = $user['school_id'];
            $_SESSION['school_name'] = $user['school_name'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'teacher') {
                header('Location: ../controller/teacher_controller.php?action=dashboard');
            } else {
                header('Location: ../controller/dashboard_controller.php');
            }
            exit;
        } else {
            $_SESSION['error'] = "Email atau password salah";
            header('Location: ../controller/auth_controller.php?action=login');
            exit;
        }
    }
}

function logout() {
    session_destroy();
    header('Location: ../view/login.php');
    exit;
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action == 'register') {
        register();
    } elseif ($action == 'login') {
        login();
    }
} elseif (isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'login':
            include '../view/login.php';
            break;
        case 'register':
            include '../view/register.php';
            break;
        case 'dashboard':
            if (!isset($_SESSION['user_id'])) {
                header('Location: ../controller/auth_controller.php?action=login');
                exit;
            }
            header('Location: ../controller/dashboard_controller.php');
            exit;
            break;
        case 'logout':
            logout();
            break;
        default:
            header('HTTP/1.0 404 Not Found');
            echo 'Action not found';
            break;
    }
} else {
    // Default action: check session and redirect
    if (isset($_SESSION['user_id'])) {
        header('Location: ../controller/auth_controller.php?action=dashboard');
    } else {
        header('Location: ../controller/auth_controller.php?action=login');
    }
    exit;
}
?>