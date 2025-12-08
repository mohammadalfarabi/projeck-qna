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

if (!isset($_SESSION['user_id'])) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit;
    } else {
        redirect('index.php?controller=auth&action=login');
        return;
    }
}

require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../model/vote_model.php';
require_once __DIR__ . '/../model/answer_model.php';

function vote_action() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
        if (!isset($_POST['answer_id'], $_POST['vote_value'])) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(['success' => false, 'message' => 'Invalid vote request.']);
                exit;
            } else {
                set_message('error', "Invalid vote request.");
                redirect('index.php?controller=dashboard&action=index');
                return;
            }
        }

        $answer_id = $_POST['answer_id'];
        $vote_value = $_POST['vote_value'];
        $user_id = $_SESSION['user_id'];

        if (add_vote($user_id, $answer_id, $vote_value)) {
            $new_vote_count = get_vote_count($answer_id);
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(['success' => true, 'message' => 'Vote berhasil', 'new_vote_count' => $new_vote_count]);
                exit;
            } else {
                set_message('success', "Vote berhasil");
                // Retrieve question_id associated with answer for redirect
                $answer = get_answer_by_id($answer_id);
                if ($answer && isset($answer['question_id'])) {
                    $question_id = $answer['question_id'];
                    redirect('index.php?controller=forum&action=question_detail&id=' . $question_id);
                } else {
                    redirect('index.php?controller=dashboard&action=index');
                }
                return;
            }
        } else {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(['success' => false, 'message' => 'Gagal melakukan vote']);
                exit;
            } else {
                set_message('error', "Gagal melakukan vote");
                redirect('index.php?controller=dashboard&action=index');
                return;
            }
        }
    } else {
        redirect('index.php?controller=dashboard&action=index');
    }
}

// Auto execute vote action when controller is loaded
vote_action();
?>
