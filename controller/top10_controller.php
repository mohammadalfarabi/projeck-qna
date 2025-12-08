<?php

session_start();


// Helper functions (since they are defined in index.php but not available here)
if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: $url");
        exit;
    }
}

if (!function_exists('load_view')) {
    function load_view($view, $data = []) {
        extract($data);
        include "view/$view.php";
    }
}

if (!isset($_SESSION['user_id'])) {
    redirect('index.php?controller=auth&action=login');
    return;
}

require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../model/question_model.php';
require_once __DIR__ . '/../model/user_model.php';

function index_action() {
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

    load_view('top10', [
        'top_questions' => $top_questions,
        'top_answers' => $top_answers,
        'display_type' => $display_type,
        'user' => $user,
        'user_questions' => $user_questions
    ]);
}

function questions_action() {
    $top_questions = get_top_questions_by_comments($_SESSION['school_id']);
    
    $user = get_user_by_id($_SESSION['user_id']);
    $user_questions = get_user_questions($_SESSION['user_id']);

    load_view('top10', [
        'top_questions' => $top_questions,
        'top_answers' => [],
        'display_type' => 'questions',
        'user' => $user,
        'user_questions' => $user_questions
    ]);
}

function answers_action() {
    $top_answers = get_top_answers_by_votes_by_school($_SESSION['school_id']);
    
    $user = get_user_by_id($_SESSION['user_id']);
    $user_questions = get_user_questions($_SESSION['user_id']);

    load_view('top10', [
        'top_questions' => [],
        'top_answers' => $top_answers,
        'display_type' => 'answers',
        'user' => $user,
        'user_questions' => $user_questions
    ]);
}
?>
