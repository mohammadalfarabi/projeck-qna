
<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../model/user_model.php';
require_once __DIR__ . '/../model/school_model.php';

function login_action() {
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
                set_message('error', "Role yang dipilih tidak sesuai dengan akun");
                redirect('index.php?controller=auth&action=login');
                return;
            }

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['school_id'] = $user['school_id'];
            $_SESSION['school_name'] = $user['school_name'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'teacher') {
                redirect('index.php?controller=teacher&action=dashboard');
            } else {
                redirect('index.php?controller=dashboard&action=index');
            }
            return;
        } else {
            set_message('error', "Email atau password salah");
            redirect('index.php?controller=auth&action=login');
            return;
        }
    }
    
    // Tampilkan login form
    load_view('login');
}

function register_action() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $school_id = $_POST['school_id'];
        $role = $_POST['role'] ?? '';
        $new_school_name = trim($_POST['new_school_name'] ?? '');

        // Validasi
        if (empty($name) || empty($email) || empty($password) || (empty($school_id) && empty($new_school_name)) || empty($role)) {
            set_message('error', "Semua field harus diisi");
            redirect('index.php?controller=auth&action=register');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            set_message('error', "Email tidak valid");
            redirect('index.php?controller=auth&action=register');
            return;
        }

        // Cek apakah email sudah terdaftar
        $existing_user = get_user_by_email($email);
        if ($existing_user) {
            set_message('error', "Email sudah terdaftar");
            redirect('index.php?controller=auth&action=register');
            return;
        }

        // Jika user memasukkan nama sekolah baru, tambahkan ke database
        if (!empty($new_school_name)) {
            $new_school_id = add_school($new_school_name);
            if ($new_school_id === false) {
                set_message('error', "Gagal menambahkan sekolah baru");
                redirect('index.php?controller=auth&action=register');
                return;
            }
            $school_id = $new_school_id;
        }

        // Buat user baru tanpa password hashing (plain text)
        if (create_user($name, $email, $password, $school_id, $role)) {
            set_message('success', "Registrasi berhasil. Silakan login.");
            redirect('index.php?controller=auth&action=login');
            return;
        } else {
            set_message('error', "Registrasi gagal");
            redirect('index.php?controller=auth&action=register');
            return;
        }
    }
    
    // Tampilkan register form
    load_view('register');
}

function logout_action() {
    session_destroy();
    redirect('index.php?controller=auth&action=login');
}

function index_action() {
    // Default action - redirect based on session
    if (isset($_SESSION['user_id'])) {
        if ($_SESSION['role'] === 'teacher') {
            redirect('index.php?controller=teacher&action=dashboard');
        } else {
            redirect('index.php?controller=dashboard&action=index');
        }
    } else {
        redirect('index.php?controller=auth&action=login');
    }
}
?>
