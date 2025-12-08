<?php
require_once __DIR__ . '/../koneksi.php';

function get_answers_by_question($question_id) {
    global $conn;

    $question_id = mysqli_real_escape_string($conn, $question_id);

    // pakai backtick untuk aman jika ada nama tabel/kolom reserved
    $query = "
        SELECT a.answer_id, a.question_id, a.user_id, a.body, a.created_at, u.name AS user_name
        FROM `answer` a
        JOIN `user` u ON a.user_id = u.user_id
        WHERE a.question_id = '$question_id'
        ORDER BY a.created_at DESC
    ";

    $result = mysqli_query($conn, $query);
    if (!$result) {
        // debug: kembalikan array kosong dan log error (boleh dihapus di production)
        error_log('DB error get_answers_by_question: ' . mysqli_error($conn));
        return [];
    }

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

    $query = "
        INSERT INTO `answer` (question_id, user_id, body, created_at)
        VALUES ('$question_id', '$user_id', '$body', NOW())
    ";

    $res = mysqli_query($conn, $query);
    if (!$res) {
        error_log('DB error create_answer: ' . mysqli_error($conn));
    }
    return $res;
}


function get_last_answer_by_user($user_id) {
    global $conn;

    $user_id = mysqli_real_escape_string($conn, $user_id);

    $query = "
        SELECT answer_id, question_id, user_id, body, created_at
        FROM `answer`
        WHERE user_id = '$user_id'
        ORDER BY created_at DESC
        LIMIT 1
    ";

    $result = mysqli_query($conn, $query);
    if (!$result) {
        error_log('DB error get_last_answer_by_user: ' . mysqli_error($conn));
        return null;
    }
    return mysqli_fetch_assoc($result);
}


function get_answer_by_id($answer_id) {
    global $conn;

    $answer_id = mysqli_real_escape_string($conn, $answer_id);

    $query = "
        SELECT answer_id, question_id, user_id, body, created_at
        FROM `answer`
        WHERE answer_id = '$answer_id'
        LIMIT 1
    ";

    $result = mysqli_query($conn, $query);
    if (!$result) {
        error_log('DB error get_answer_by_id: ' . mysqli_error($conn));
        return null;
    }
    return mysqli_fetch_assoc($result);
}
?>
