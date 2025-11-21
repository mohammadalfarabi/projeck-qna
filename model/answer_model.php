<?php
// model/answer_model.php

function answer_create($conn, $question_id, $user_id, $body){
    $question_id = (int)$question_id;
    $user_id = (int)$user_id;
    $body = mysqli_real_escape_string($conn, $body);
    $now = date('Y-m-d H:i:s');
    $q = "INSERT INTO answer (question_id, user_id, body, created_at) VALUES ($question_id, $user_id, '$body', '$now')";
    $ok = mysqli_query($conn, $q);
    if ($ok) {
        $aid = mysqli_insert_id($conn);
        // add gamification points
        user_add_points($conn, $user_id, 10); // +10 for answer
        mysqli_query($conn, "INSERT INTO activity_log (user_id, action, detail, created_at) VALUES ($user_id, 'answer_create', 'answer_id:$aid', '".date('Y-m-d H:i:s')."')");
        return $aid;
    }
    return false;
}

function answer_edit($conn, $answer_id, $user_id, $new_body){
    $answer_id = (int)$answer_id;
    $user_id = (int)$user_id;
    $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT body FROM answer WHERE answer_id=$answer_id LIMIT 1"));
    if ($old) {
        $old_body = mysqli_real_escape_string($conn, $old['body']);
        mysqli_query($conn, "INSERT INTO answer_edit_log (answer_id, user_id, old_body, edited_at) VALUES ($answer_id, $user_id, '$old_body', '".date('Y-m-d H:i:s')."')");
    }
    $body = mysqli_real_escape_string($conn, $new_body);
    return mysqli_query($conn, "UPDATE answer SET body='$body', updated_at='".date('Y-m-d H:i:s')."' WHERE answer_id=$answer_id");
}

function answer_delete($conn, $answer_id){
    $answer_id = (int)$answer_id;
    return mysqli_query($conn, "DELETE FROM answer WHERE answer_id=$answer_id");
}

function answer_get_by_question($conn, $question_id){
    $question_id = (int)$question_id;
    $q = "SELECT a.*, u.name, IFNULL((SELECT SUM(v.vote_value) FROM vote v WHERE v.answer_id=a.answer_id),0) AS score
          FROM answer a JOIN user u ON a.user_id=u.user_id
          WHERE a.question_id=$question_id ORDER BY a.created_at ASC";
    return mysqli_query($conn, $q);
}

function answer_top10_by_school($conn, $school_id){
    $school_id = (int)$school_id;
    $q = "SELECT a.*, u.name, IFNULL(SUM(v.vote_value),0) AS score
          FROM answer a JOIN user u ON a.user_id=u.user_id
          LEFT JOIN vote v ON v.answer_id=a.answer_id
          WHERE u.school_id=$school_id
          GROUP BY a.answer_id
          ORDER BY score DESC LIMIT 10";
    return mysqli_query($conn, $q);
}

/**
 * Menghitung jumlah like pada jawaban
 */
function answer_like_count($conn, $answer_id){
    $answer_id = (int)$answer_id;
    $r = mysqli_query($conn, "
        SELECT COUNT(*) AS c 
        FROM vote 
        WHERE answer_id = $answer_id
    ");
    $d = mysqli_fetch_assoc($r);
    return $d ? (int)$d['c'] : 0;
}

?>
