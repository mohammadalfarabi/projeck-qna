<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../controller/auth_controller.php?action=login');
    exit;
}

require_once '../koneksi.php';
require_once '../model/question_model.php';
require_once '../model/user_model.php';

$display_type = $_GET['type'] ?? 'questions';

if ($display_type === 'answers') {
    $top_answers = get_top_answers_by_votes_by_school($_SESSION['school_id']);
    $top_questions = [];
} else {
    $top_questions = get_top_questions_by_comments($_SESSION['school_id']);
    $top_answers = [];
}

$user = get_user_by_id($_SESSION['user_id']);
$user_questions = get_user_questions($_SESSION['user_id']);

include '../view/top10.php';
?>