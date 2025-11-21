<?php
session_start();
require 'koneksi.php';

/* =============================
   = LOAD CONTROLLER UTAMA     =
   ============================= */
require_once 'controller/auth_controller.php';
require_once 'controller/forum_controller.php';
require_once 'controller/vote_controller.php';
require_once 'controller/top10_controller.php';
require_once 'controller/analytics_controller.php';

/* =============================
   = ROUTER                    =
   ============================= */
$page = isset($_GET['page']) ? $_GET['page'] : 'login';

switch ($page) {

    /* ===== AUTH ===== */
    case 'login': show_login(); break;
    case 'do_login': do_login(); break;
    case 'logout': do_logout(); break;

    case 'register': show_register(); break;
    case 'do_register': do_register(); break;

    /* ===== PROFILE ===== */
    case 'profile': show_profile(); break;
    case 'update_profile': do_update_profile(); break;
    case 'update_password': do_change_password(); break;
    case 'upload_photo': do_upload_photo(); break;

    /* ===== DASHBOARD / FORUM ===== */
    case 'dashboard': show_dashboard(); break;

    case 'ask': show_question_form(); break;
    case 'post_question': post_question(); break;

    case 'view_question': show_question(); break;

    /* ===== ANSWERS & COMMENTS ===== */
    case 'post_answer': post_answer(); break;
    case 'post_comment': post_comment(); break;

    /* ===== EDIT & DELETE ===== */
    case 'question_edit': show_question_edit(); break;
    case 'do_question_edit': do_question_edit(); break;
    case 'question_delete': do_question_delete(); break;

    case 'answer_edit': show_answer_edit(); break;
    case 'do_answer_edit': do_answer_edit(); break;
    case 'answer_delete': do_answer_delete(); break;

    case 'comment_edit': show_comment_edit(); break;
    case 'do_comment_edit': do_comment_edit(); break;
    case 'comment_delete': do_comment_delete(); break;

    /* ===== VOTES ===== */
    case 'vote_answer': vote_answer(); break;   // vote jawaban
    case 'like_question': like_question(); break;  // like / unlike pertanyaan

    /* ===== TOP 10 ===== */
    case 'top10': show_top10(); break;

    /* ===== ANALYTICS ===== */
    case 'analytics': show_analytics(); break;

    /* ===== ADMIN ===== */
    case 'admin_users': admin_users(); break;
    case 'admin_delete_user': admin_delete_user(); break;
    case 'admin_change_role': admin_change_role(); break;

    /* ===== HISTORY ===== */
    case 'history': show_history(); break;

    /* ===== DEFAULT ===== */
    default:
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?page=dashboard");
        } else {
            show_login();
        }
        break;
}
?>
