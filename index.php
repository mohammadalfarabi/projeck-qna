<?php
session_start();

// Helper Functions
function load_view($view, $data = []) {
    extract($data);
    include "view/$view.php";
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function set_message($type, $message) {
    $_SESSION[$type] = $message;
}

function get_message($type) {
    if (isset($_SESSION[$type])) {
        $message = $_SESSION[$type];
        unset($_SESSION[$type]);
        return $message;
    }
    return null;
}

// Router Logic
$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';

// Default controller untuk user yang sudah login
if (!isset($_SESSION['user_id']) && $controller != 'auth') {
    $controller = 'auth';
    $action = 'login';
}

// Validasi controller yang tersedia
$allowed_controllers = ['auth', 'dashboard', 'forum', 'teacher', 'top10', 'vote'];
if (!in_array($controller, $allowed_controllers)) {
    $controller = 'auth';
    $action = 'login';
}

// Load controller
$controller_file = "controller/{$controller}_controller.php";

if (file_exists($controller_file)) {
    require_once $controller_file;
    
    // Panggil fungsi action dari controller
    $function_name = $action . '_action';
    if (function_exists($function_name)) {
        $function_name();
    } else {
        // Jika action tidak ditemukan, gunakan default
        $function_name = $controller . '_action';
        if (function_exists($function_name)) {
            $function_name();
        } else {
            // Default redirect ke dashboard
            if (isset($_SESSION['user_id'])) {
                redirect('index.php?controller=dashboard&action=index');
            } else {
                redirect('index.php?controller=auth&action=login');
            }
        }
    }
} else {
    // Controller tidak ditemukan
    if (isset($_SESSION['user_id'])) {
        redirect('index.php?controller=dashboard&action=index');
    } else {
        redirect('index.php?controller=auth&action=login');
    }
}
?>
