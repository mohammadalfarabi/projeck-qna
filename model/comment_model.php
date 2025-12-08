<?php

require_once __DIR__ . '/../koneksi.php';

function get_comments_by_question($question_id) {
    global $conn;

    $question_id = mysqli_real_escape_string($conn, $question_id);

    $query = "
        SELECT 
            c.comment_id, c.user_id, c.question_id, c.answer_id, 
            c.body, c.created_at,
            u.name AS user_name
        FROM `comment` c
        JOIN `user` u ON c.user_id = u.user_id
        WHERE c.question_id = '$question_id'
        ORDER BY c.created_at DESC
    ";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        error_log('DB error get_comments_by_question: ' . mysqli_error($conn));
        return [];
    }

    $comments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $comments[] = $row;
    }
    return $comments;
}


function get_comments_by_answer($answer_id) {
    global $conn;

    $answer_id = mysqli_real_escape_string($conn, $answer_id);

    $query = "
        SELECT 
            c.comment_id, c.user_id, c.question_id, c.answer_id, 
            c.body, c.created_at,
            u.name AS user_name
        FROM `comment` c
        JOIN `user` u ON c.user_id = u.user_id
        WHERE c.answer_id = '$answer_id'
        ORDER BY c.created_at DESC
    ";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        error_log('DB error get_comments_by_answer: ' . mysqli_error($conn));
        return [];
    }

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

    // if NULL, langsung tulis NULL tanpa kutip
    if (empty($answer_id)) {
        $answer_id_sql = "NULL";
    } else {
        $answer_id_sql = "'" . mysqli_real_escape_string($conn, $answer_id) . "'";
    }

    $body = mysqli_real_escape_string($conn, $body);

    $query = "
        INSERT INTO `comment` (user_id, question_id, answer_id, body, created_at)
        VALUES ('$user_id', '$question_id', $answer_id_sql, '$body', NOW())
    ";

    $res = mysqli_query($conn, $query);

    if (!$res) {
        error_log('DB error create_comment: ' . mysqli_error($conn));
    }

    return $res;
}


function get_last_comment_by_user($user_id) {
    global $conn;

    $user_id = mysqli_real_escape_string($conn, $user_id);

    $query = "
        SELECT comment_id, user_id, question_id, answer_id, body, created_at
        FROM `comment`
        WHERE user_id = '$user_id'
        ORDER BY created_at DESC
        LIMIT 1
    ";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        error_log('DB error get_last_comment_by_user: ' . mysqli_error($conn));
        return null;
    }

    return mysqli_fetch_assoc($result);
}

?>
