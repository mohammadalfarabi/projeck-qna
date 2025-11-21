<?php
require_once 'model/user_model.php';

/* ===============================
   ADMIN – LIST USER
   =============================== */
function admin_users()
{
    global $conn;
    $users = user_list_all($conn); // <-- pakai yang sudah ada
    require 'view/admin_users.php';
}


/* ===============================
   ADMIN – DELETE USER
   =============================== */
function admin_delete_user()
{
    global $conn;

    if (!isset($_GET['id'])) {
        header("Location: index.php?page=admin_users");
        exit;
    }

    $user_id = intval($_GET['id']);
    user_delete($conn, $user_id);

    header("Location: index.php?page=admin_users");
    exit;
}


/* ===============================
   ADMIN – CHANGE ROLE USER
   =============================== */
function admin_change_role()
{
    global $conn;

    if (!isset($_POST['user_id']) || !isset($_POST['role'])) {
        header("Location: index.php?page=admin_users");
        exit;
    }

    $user_id = intval($_POST['user_id']);
    $role    = mysqli_real_escape_string($conn, $_POST['role']);

    user_change_role($conn, $user_id, $role);

    header("Location: index.php?page=admin_users");
    exit;
}
?>
