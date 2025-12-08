<?php

require_once __DIR__ . '/../koneksi.php';

function get_comments_by_question($question_id) {
    global $conn;
    $question_id = mysqli_real_escape_string($conn, $question_id);
    $query = "SELECT c.*, u.name as user_name 
              FROM comment c 
              JOIN user u ON c.user_id = u.user_id 
              WHERE c.question_id = '$question_id' 
              ORDER BY c.created_at DESC";
    $result = mysqli_query($conn, $query);
    $comments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $comments[] = $row;
    }
    return $comments;
}

function get_comments_by_answer($answer_id) {
    global $conn;
    $answer_id = mysqli_real_escape_string($conn, $answer_id);
    $query = "SELECT c.*, u.name as user_name 
              FROM comment c 
              JOIN user u ON c.user_id = u.user_id 
              WHERE c.answer_id = '$answer_id' 
              ORDER BY c.created_at DESC";
    $result = mysqli_query($conn, $query);
    $comments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $comments[] = $row;
    }
    return $comments;
}

function create_comment($user_id, $question_id, $answer_id, $body) {
    global $conn;
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $question_id = mysqli_real_escape_string($conn, $question_id);

    if (empty($answer_id)) {
        $answer_id_sql = "NULL";
    } else {
        $answer_id_sql = "'" . mysqli_real_escape_string($conn, $answer_id) . "'";
    }

    $body = mysqli_real_escape_string($conn, $body);

    $query = "INSERT INTO comment (user_id, question_id, answer_id, body, created_at)
              VALUES ('$user_id', '$question_id', $answer_id_sql, '$body', NOW())";
    return mysqli_query($conn, $query);
}

function get_last_comment_by_user($user_id) {
    global $conn;
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $query = "SELECT * FROM comment WHERE user_id = '$user_id' ORDER BY created_at DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}
?>