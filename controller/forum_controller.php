<?php

require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../model/question_model.php';
require_once __DIR__ . '/../model/answer_model.php';
require_once __DIR__ . '/../model/comment_model.php';
require_once __DIR__ . '/../model/user_model.php';
require_once __DIR__ . '/../model/vote_model.php';


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

function question_detail_action() {
    if (!isset($_SESSION['user_id'])) {
        redirect('index.php?controller=auth&action=login');
        return;
    }

    if (!isset($_GET['id'])) {
        set_message('error', "ID pertanyaan tidak ditemukan");
        redirect('index.php?controller=dashboard&action=index');
        return;
    }

    $question_id = intval($_GET['id']);
    $question = get_question_by_id($question_id);

    if (!$question) {
        set_message('error', "Pertanyaan tidak ditemukan");
        redirect('index.php?controller=dashboard&action=index');
        return;
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


    load_view('question_detail', [
        'question' => $question,
        'question_id' => $question_id, // Fix: Tambahkan question_id untuk view
        'answers' => $answers,
        'question_comments' => $question_comments,
        'answer_comments' => $answer_comments,
        'user' => $user
    ]);
}

function create_question_action() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
        $title = $_POST['title'];
        $body = $_POST['body'];
        $tag_id = $_POST['tag_id'] ?? null;
        $user_id = $_SESSION['user_id'];

        if (create_question($user_id, $title, $body, $tag_id)) {
            set_message('success', "Pertanyaan berhasil diposting");
            redirect('index.php?controller=dashboard&action=index');
        } else {
            set_message('error', "Gagal memposting pertanyaan");
            redirect('index.php?controller=dashboard&action=index');
        }
    } else {
        redirect('index.php?controller=dashboard&action=index');
    }
}

function create_answer_action() {
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
                set_message('success', "Jawaban berhasil diposting");
                redirect('index.php?controller=forum&action=question_detail&id=' . $question_id);
            }
        } else {
            if ($is_ajax) {
                echo json_encode(['success' => false, 'message' => 'Gagal memposting jawaban']);
            } else {
                set_message('error', "Gagal memposting jawaban");
                redirect('index.php?controller=forum&action=question_detail&id=' . $question_id);
            }
        }
        if ($is_ajax) {
            exit;
        }
    } else {
        redirect('index.php?controller=dashboard&action=index');
    }
}

function create_comment_action() {
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
                set_message('success', "Komentar berhasil diposting");
                // Redirect back to question detail page
                if ($question_id) {
                    redirect('index.php?controller=forum&action=question_detail&id=' . $question_id);
                } elseif ($answer_id) {
                    // Get question_id from answer if not provided
                    $answer = get_answer_by_id($answer_id);
                    if ($answer && isset($answer['question_id'])) {
                        redirect('index.php?controller=forum&action=question_detail&id=' . $answer['question_id']);
                    } else {
                        redirect('index.php?controller=dashboard&action=index');
                    }
                } else {
                    redirect('index.php?controller=dashboard&action=index');
                }
            }
        } else {
            global $conn;
            $error = mysqli_error($conn);
            if ($is_ajax) {
                echo json_encode(['success' => false, 'message' => 'Gagal memposting komentar: ' . $error]);
            } else {
                set_message('error', "Gagal memposting komentar: " . $error);
                // Redirect back to question detail page
                if ($question_id) {
                    redirect('index.php?controller=forum&action=question_detail&id=' . $question_id);
                } elseif ($answer_id) {
                    // Get question_id from answer if not provided
                    $answer = get_answer_by_id($answer_id);
                    if ($answer && isset($answer['question_id'])) {
                        redirect('index.php?controller=forum&action=question_detail&id=' . $answer['question_id']);
                    } else {
                        redirect('index.php?controller=dashboard&action=index');
                    }
                } else {
                    redirect('index.php?controller=dashboard&action=index');
                }
            }
        }
        if ($is_ajax) {
            exit;
        }
    } else {
        redirect('index.php?controller=dashboard&action=index');
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

function index_action() {
    // Default action - redirect to dashboard
    redirect('index.php?controller=dashboard&action=index');
}
?>
