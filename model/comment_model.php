<?php
// model/comment_model.php

// Perbaikan: $body dipindah ke depan → parameter wajib dulu
function comment_create($conn, $user_id, $body, $question_id=null, $answer_id=null){
    $user_id = (int)$user_id;
    $question_id = $question_id ? (int)$question_id : 'NULL';
    $answer_id = $answer_id ? (int)$answer_id : 'NULL';
    
    $body = mysqli_real_escape_string($conn, $body);
    $now = date('Y-m-d H:i:s');

    $q = "INSERT INTO comment (user_id, question_id, answer_id, body, created_at) 
          VALUES ($user_id, $question_id, $answer_id, '$body', '$now')";

    $res = mysqli_query($conn, $q);
    if ($res) {
        user_add_points($conn, $user_id, 2); // +2 points for comment
        mysqli_query($conn, 
            "INSERT INTO activity_log (user_id, action, detail, created_at) 
             VALUES ($user_id, 'comment_create', 'question:$question_id;answer:$answer_id', '".date('Y-m-d H:i:s')."')"
        );
    }
    return $res;
}

function comment_get_by_question($conn, $question_id){
    $question_id = (int)$question_id;
    $q = "SELECT c.*, u.name 
          FROM comment c 
          JOIN user u ON c.user_id=u.user_id 
          WHERE c.question_id=$question_id 
          ORDER BY c.created_at ASC";
    return mysqli_query($conn, $q);
}

function comment_get_by_answer($conn, $answer_id){
    $answer_id = (int)$answer_id;
    $q = "SELECT c.*, u.name 
          FROM comment c 
          JOIN user u ON c.user_id=u.user_id 
          WHERE c.answer_id=$answer_id 
          ORDER BY c.created_at ASC";
    return mysqli_query($conn, $q);
}

function comment_edit($conn, $comment_id, $user_id, $new_body){
    $comment_id = (int)$comment_id;
    $user_id = (int)$user_id;

    $old = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT body FROM comment WHERE id=$comment_id LIMIT 1")
    );

    if ($old) {
        $old_body = mysqli_real_escape_string($conn, $old['body']);
        // Bisa ditambahkan log edit
    }

    $body = mysqli_real_escape_string($conn, $new_body);
    return mysqli_query(
        $conn, 
        "UPDATE comment 
         SET body='$body', updated_at='".date('Y-m-d H:i:s')."' 
         WHERE id=$comment_id AND user_id=$user_id"
    );
}

function comment_delete($conn, $comment_id){
    $comment_id = (int)$comment_id;
    return mysqli_query($conn, "DELETE FROM comment WHERE id=$comment_id");
}

function comment_count_answer($conn, $answer_id){
    $answer_id = (int)$answer_id;
    $r = mysqli_query($conn, "
        SELECT COUNT(*) AS c 
        FROM comment 
        WHERE answer_id = $answer_id
    ");
    $d = mysqli_fetch_assoc($r);
    return $d ? (int)$d['c'] : 0;
}

?>
