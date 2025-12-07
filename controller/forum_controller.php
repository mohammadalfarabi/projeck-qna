<?php

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

include "../koneksi.php";
require_once '../model/question_model.php';
require_once '../model/answer_model.php';
require_once '../model/comment_model.php';
require_once '../model/user_model.php';
require_once '../model/vote_model.php';

function post_question() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
        $title = $_POST['title'];
        $body = $_POST['body'];
        $tag_id = $_POST['tag_id'] ?? null;
        $user_id = $_SESSION['user_id'];

        if (create_question($user_id, $title, $body, $tag_id)) {
            $_SESSION['success'] = "Pertanyaan berhasil diposting";
        } else {
            $_SESSION['error'] = "Gagal memposting pertanyaan";
        }

        header('Location: ../index.php');
        exit;
    }
}

function post_answer() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
        $question_id = $_POST['question_id'];
        $body = $_POST['body'];
        $user_id = $_SESSION['user_id'];

        $is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if (create_answer($question_id, $user_id, $body)) {
            if ($is_ajax) {
                // Get the new answer data
                $new_answer = get_last_answer_by_user($user_id);
                $user = get_user_by_id($user_id);
                $answer_data = [
                    'answer_id' => $new_answer['answer_id'],
                    'user_name' => $user['name'],
                    'body' => $new_answer['body'],
                    'created_at' => $new_answer['created_at'],
                    'total_votes' => 0
                ];
                echo json_encode(['success' => true, 'message' => 'Jawaban berhasil diposting', 'answer' => $answer_data]);
            } else {
                $_SESSION['success'] = "Jawaban berhasil diposting";
                header('Location: ../view/question_detail.php?id=' . $question_id);
            }
        } else {
            if ($is_ajax) {
                echo json_encode(['success' => false, 'message' => 'Gagal memposting jawaban']);
            } else {
                $_SESSION['error'] = "Gagal memposting jawaban";
                header('Location: ../view/question_detail.php?id=' . $question_id);
            }
        }
        if ($is_ajax) {
            exit;
        }
        exit;
    }
}

function post_comment() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
        $question_id = $_POST['question_id'] ?? null;
        $answer_id = $_POST['answer_id'] ?? null;
        $body = $_POST['body'];
        $user_id = $_SESSION['user_id'];

        $is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if (create_comment($user_id, $question_id, $answer_id, $body)) {
            if ($is_ajax) {
                // Get the new comment data
                $new_comment = get_last_comment_by_user($user_id);
                $user = get_user_by_id($user_id);
                $comment_data = [
                    'comment_id' => $new_comment['comment_id'],
                    'user_name' => $user['name'],
                    'body' => $new_comment['body'],
                    'created_at' => $new_comment['created_at']
                ];
                echo json_encode(['success' => true, 'message' => 'Komentar berhasil diposting', 'comment' => $comment_data]);
            } else {
                $_SESSION['success'] = "Komentar berhasil diposting";
                // Redirect back to question detail page
                if ($question_id) {
                    header('Location: ../view/question_detail.php?id=' . $question_id);
                } elseif ($answer_id) {
                    // Get question_id from answer if not provided
                    $answer = get_answer_by_id($answer_id);
                    if ($answer && isset($answer['question_id'])) {
                        header('Location: ../view/question_detail.php?id=' . $answer['question_id']);
                    } else {
                        header('Location: ../view/dashboard.php');
                    }
                } else {
                    header('Location: ../view/dashboard.php');
                }
            }
        } else {
            global $conn;
            $error = mysqli_error($conn);
            if ($is_ajax) {
                echo json_encode(['success' => false, 'message' => 'Gagal memposting komentar: ' . $error]);
            } else {
                $_SESSION['error'] = "Gagal memposting komentar: " . $error;
                // Redirect back to question detail page
                if ($question_id) {
                    header('Location: ../view/question_detail.php?id=' . $question_id);
                } elseif ($answer_id) {
                    // Get question_id from answer if not provided
                    $answer = get_answer_by_id($answer_id);
                    if ($answer && isset($answer['question_id'])) {
                        header('Location: ../view/question_detail.php?id=' . $answer['question_id']);
                    } else {
                        header('Location: ../view/dashboard.php');
                    }
                } else {
                    header('Location: ../view/dashboard.php');
                }
            }
        }
        if ($is_ajax) {
            exit;
        }
        exit;
    }
}

function get_top_answers() {
    global $conn;
    $query = "SELECT a.*, u.name as user_name, q.title as question_title, SUM(v.vote_value) as total_votes
              FROM answer a
              JOIN user u ON a.user_id = u.user_id
              JOIN question q ON a.question_id = q.question_id
              LEFT JOIN vote v ON a.answer_id = v.answer_id
              GROUP BY a.answer_id
              ORDER BY total_votes DESC
              LIMIT 10";
    $result = mysqli_query($conn, $query);
    $top_answers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $top_answers[] = $row;
    }
    return $top_answers;
}





function question_detail() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../view/login.php');
        exit;
    }

    if (!isset($_GET['id'])) {
        echo "<p>ID pertanyaan tidak ditemukan.</p>";
        exit;
    }

    $question_id = intval($_GET['id']);
    $question = get_question_by_id($question_id);

    if (!$question) {
        echo "<p>Pertanyaan tidak ditemukan.</p>";
        exit;
    }

    // Get answers for question
    $answers = get_answers_by_question($question_id);

    // Get comments for question
    $question_comments = get_comments_by_question($question_id);

    // Get comments for each answer
    $answer_comments = [];
    if ($answers) {
        foreach ($answers as &$answer) {
            $answer_comments[$answer['answer_id']] = get_comments_by_answer($answer['answer_id']);
            $answer['total_votes'] = get_vote_count($answer['answer_id']);
        }
    }

    $user = get_user_by_id($_SESSION['user_id']);

    include '../view/question_detail.php';
}

if (isset($_GET['action'])) {
    if ($_GET['action'] == "question_detail") {
        question_detail();
    }
}

if (isset($_POST['action'])) {
    if ($_POST['action'] == "create_question") {
        post_question();
    } elseif ($_POST['action'] == "create_answer") {
        post_answer();
    } elseif ($_POST['action'] == "create_comment") {
        post_comment();
    }
}
?>