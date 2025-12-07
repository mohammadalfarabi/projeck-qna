<?php
session_start();
require_once '../koneksi.php';
require_once '../model/vote_model.php';
require_once '../model/answer_model.php';

function vote_answer() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
        if (!isset($_POST['answer_id'], $_POST['vote_value'])) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(['success' => false, 'message' => 'Invalid vote request.']);
                exit;
            } else {
                $_SESSION['error'] = "Invalid vote request.";
                header('Location: ../view/dashboard.php');
                exit;
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
                $_SESSION['success'] = "Vote berhasil";
                // Retrieve question_id associated with answer for redirect
                $answer = get_answer_by_id($answer_id);
                if ($answer && isset($answer['question_id'])) {
                    $question_id = $answer['question_id'];
                    header('Location: ../view/question_detail.php?id=' . $question_id);
                } else {
                    header('Location: ../view/dashboard.php');
                }
                exit;
            }
        } else {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(['success' => false, 'message' => 'Gagal melakukan vote']);
                exit;
            } else {
                $_SESSION['error'] = "Gagal melakukan vote";
                header('Location: ../view/dashboard.php');
                exit;
            }
        }
    }
}

vote_answer();
?>