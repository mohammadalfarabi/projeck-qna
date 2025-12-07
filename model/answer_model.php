<?php
require_once '../koneksi.php';

function get_answers_by_question($question_id) {
    global $conn;
    $question_id = mysqli_real_escape_string($conn, $question_id);
    $query = "SELECT a.*, u.name as user_name 
              FROM answer a 
              JOIN user u ON a.user_id = u.user_id 
              WHERE a.question_id = '$question_id' 
              ORDER BY a.created_at DESC";
    $result = mysqli_query($conn, $query);
    $answers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $answers[] = $row;
    }
    return $answers;
}

function create_answer($question_id, $user_id, $body) {
    global $conn;
    $question_id = mysqli_real_escape_string($conn, $question_id);
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $body = mysqli_real_escape_string($conn, $body);

    $query = "INSERT INTO answer (question_id, user_id, body, created_at)
              VALUES ('$question_id', '$user_id', '$body', NOW())";
    return mysqli_query($conn, $query);
}

function get_last_answer_by_user($user_id) {
    global $conn;
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $query = "SELECT * FROM answer WHERE user_id = '$user_id' ORDER BY created_at DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

function get_answer_by_id($answer_id) {
    global $conn;
    $answer_id = mysqli_real_escape_string($conn, $answer_id);
    $query = "SELECT * FROM answer WHERE answer_id = '$answer_id'";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}
?>