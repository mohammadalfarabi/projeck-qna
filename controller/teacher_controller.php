<?php
session_start();

require_once '../model/user_model.php';
require_once '../model/question_model.php';
require_once '../model/school_model.php';

function dashboard() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
        header('Location: ../view/login.php');
        exit;
    }

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $school_id = $_SESSION['school_id'];

    if (empty($school_id)) {
        echo "Error: School ID missing in session.";
        exit;
    }

    $users = get_users_by_school($school_id);

    if ($users === false) {
        echo "Database query error on fetching users.";
        exit;
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

    include '../view/teacher_dashboard.php';
}

function edit_user_handler() {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
        header('Location: ../view/login.php');
        exit;
    }

    $user_id = $_GET['id'] ?? null;
    if (!$user_id) {
        header('Location: ../controller/teacher_controller.php?action=dashboard');
        exit;
    }

    $user = get_user_by_id($user_id);
    if (!$user) {
        echo "User not found.";
        exit;
    }

    // Check if the user is in the same school as the teacher
    if ($user['school_id'] != $_SESSION['school_id']) {
        echo "You can only edit users in your school.";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $role = $_POST['role'] ?? '';
        $school_id = $_POST['school_id'] ?? '';

        $result = update_user($user_id, $name, $email, $role, $school_id);
        if ($result === true) {
            header('Location: ../controller/teacher_controller.php?action=dashboard');
            exit;
        } else {
            echo "Error updating user: $result";
        }
    } else {
        include '../view/edit_user.php';
    }
}

function delete_user_handler() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
        header('Location: ../view/login.php');
        exit;
    }

    $user_id = $_GET['id'] ?? null;
    if (!$user_id) {
        header('Location: ../controller/teacher_controller.php?action=dashboard');
        exit;
    }

    $user = get_user_by_id($user_id);
    if (!$user) {
        echo "User not found.";
        exit;
    }

    // Check if the user is in the same school as the teacher
    if ($user['school_id'] != $_SESSION['school_id']) {
        echo "You can only delete users in your school.";
        exit;
    }

    if (delete_user($user_id)) {
        header('Location: ../controller/teacher_controller.php?action=dashboard');
        exit;
    } else {
        echo "Error deleting user.";
    }
}

$action = $_GET['action'] ?? 'dashboard';

switch ($action) {
    case 'dashboard':
        dashboard();
        break;
    case 'edit_user':
        edit_user_handler();
        break;
    case 'delete_user':
        delete_user_handler();
        break;
    default:
        header('HTTP/1.0 404 Not Found');
        echo 'Action not found';
        break;
}
?>