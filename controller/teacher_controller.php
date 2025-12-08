<?php

session_start();


// Helper functions (since they are defined in index.php but not available here)
if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: $url");
        exit;
    }
}

if (!function_exists('set_message')) {
    function set_message($type, $message) {
        $_SESSION[$type] = $message;
    }
}

if (!function_exists('load_view')) {
    function load_view($view, $data = []) {
        extract($data);
        include "view/$view.php";
    }
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    redirect('index.php?controller=auth&action=login');
    return;
}

require_once __DIR__ . '/../model/user_model.php';
require_once __DIR__ . '/../model/question_model.php';
require_once __DIR__ . '/../model/school_model.php';

function dashboard_action() {
    $school_id = $_SESSION['school_id'];

    if (empty($school_id)) {
        set_message('error', "Error: School ID missing in session.");
        redirect('index.php?controller=auth&action=login');
        return;
    }

    $users = get_users_by_school($school_id);

    if ($users === false) {
        set_message('error', "Database query error on fetching users.");
        redirect('index.php?controller=auth&action=login');
        return;
    }

    // Handle user search
    $search_name = $_GET['search_name'] ?? '';
    if (!empty($search_name)) {
        $users = array_filter($users, function($user) use ($search_name) {
            return stripos($user['name'], $search_name) !== false;
        });
    }

    // Set variables for navbar
    $user = get_user_by_id($_SESSION['user_id']);
    $user_questions = get_user_questions($_SESSION['user_id']);

    load_view('teacher_dashboard', [
        'users' => $users,
        'user' => $user,
        'user_questions' => $user_questions,
        'search_name' => $search_name
    ]);
}

function edit_user_action() {
    $user_id = $_GET['id'] ?? null;
    if (!$user_id) {
        set_message('error', "User ID is required");
        redirect('index.php?controller=teacher&action=dashboard');
        return;
    }

    $user = get_user_by_id($user_id);
    if (!$user) {
        set_message('error', "User not found");
        redirect('index.php?controller=teacher&action=dashboard');
        return;
    }


    // Check if the user is in the same school as the teacher
    if ($user['school_id'] != $_SESSION['school_id']) {
        set_message('error', "You can only edit users in your school");
        redirect('index.php?controller=teacher&action=dashboard');
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $role = $_POST['role'] ?? '';
        $school_id = $_POST['school_id'] ?? '';

        $result = update_user($user_id, $name, $email, $role, $school_id);
        if ($result === true) {
            set_message('success', "User updated successfully");
            redirect('index.php?controller=teacher&action=dashboard');
            return;
        } else {
            set_message('error', "Error updating user: $result");
        }
    }
    
    $user_data = get_user_by_id($user_id);
    $user = get_user_by_id($_SESSION['user_id']);
    $user_questions = get_user_questions($_SESSION['user_id']);

    load_view('edit_user', [
        'user_data' => $user_data,
        'user' => $user,
        'user_questions' => $user_questions
    ]);
}

function delete_user_action() {
    $user_id = $_GET['id'] ?? null;
    if (!$user_id) {
        set_message('error', "User ID is required");
        redirect('index.php?controller=teacher&action=dashboard');
        return;
    }

    $user = get_user_by_id($user_id);
    if (!$user) {
        set_message('error', "User not found");
        redirect('index.php?controller=teacher&action=dashboard');
        return;
    }

    // Check if the user is in the same school as the teacher
    if ($user['school_id'] != $_SESSION['school_id']) {
        set_message('error', "You can only delete users in your school");
        redirect('index.php?controller=teacher&action=dashboard');
        return;
    }

    if (delete_user($user_id)) {
        set_message('success', "User deleted successfully");
        redirect('index.php?controller=teacher&action=dashboard');
        return;
    } else {
        set_message('error', "Error deleting user");
        redirect('index.php?controller=teacher&action=dashboard');
        return;
    }
}

function index_action() {
    // Default action - go to dashboard
    redirect('index.php?controller=teacher&action=dashboard');
}
?>
