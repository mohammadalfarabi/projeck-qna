<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: controller/dashboard_controller.php');
} else {
    header('Location: view/login.php');
}
exit;
?>
