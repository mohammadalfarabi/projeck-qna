<?php
// controller/vote_controller.php

require_once 'model/vote_model.php';
require_once 'model/answer_model.php';
require_once 'model/question_model.php';
require_once 'model/notify_model.php';
require_once 'model/user_model.php';

function ensure_login_vote(){
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?page=login");
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| VOTE JAWABAN (upvote / downvote)
|--------------------------------------------------------------------------
*/
function vote_answer(){
    global $conn;
    ensure_login_vote();

    $user_id = $_SESSION['user_id'];
    $answer_id = isset($_GET['answer_id']) ? (int)$_GET['answer_id'] : 0;
    $value = isset($_GET['value']) ? (int)$_GET['value'] : 0; // 1 or -1

    if ($answer_id <= 0 || !in_array($value, [1, -1])) {
        header("Location: index.php?page=dashboard");
        exit;
    }

    // Simpan vote
    $ok = vote_answer_save($conn, $answer_id, $user_id, $value);

    if ($ok) {
        // Ambil pemilik jawaban
        $ans = mysqli_fetch_assoc(mysqli_query($conn, 
            "SELECT user_id, question_id FROM answer WHERE answer_id = $answer_id LIMIT 1"
        ));

        if ($ans && $ans['user_id'] != $user_id) {

            // Jika UPVOTE → +5 poin
            if ($value == 1) {
                user_add_points($conn, $ans['user_id'], 5);
            }

            // Kirim notifikasi
            $msg = ($value == 1)
                ? "Jawaban Anda mendapat upvote!"
                : "Jawaban Anda mendapat downvote.";

            notify_create($conn, $ans['user_id'], $msg);
        }

        // Log aktivitas
        mysqli_query($conn, 
            "INSERT INTO activity_log (user_id, action, detail, created_at)
             VALUES ($user_id, 'vote_answer', 'answer:$answer_id,value:$value', NOW())"
        );
    }

    header("Location: index.php?page=view_question&id=" . $ans['question_id']);
}

/*
|--------------------------------------------------------------------------
| LIKE / UNLIKE PERTANYAAN
|--------------------------------------------------------------------------
*/
function like_question(){
    global $conn;
    ensure_login_vote();

    $user_id = $_SESSION['user_id'];
    $question_id = isset($_GET['question_id']) ? (int)$_GET['question_id'] : 0;

    if ($question_id <= 0) {
        header("Location: index.php?page=dashboard");
        exit;
    }

    // Save like/unlike
    $result = question_like_toggle($conn, $question_id, $user_id);

    // Ambil pemilik pertanyaan
    $q = mysqli_fetch_assoc(mysqli_query($conn, 
        "SELECT user_id, title FROM question WHERE question_id = $question_id LIMIT 1"
    ));

    if ($q && $q['user_id'] != $user_id) {

        // Jika LIKE → +3 poin
        if ($result == "liked") {
            user_add_points($conn, $q['user_id'], 3);
            notify_create($conn, $q['user_id'], "Pertanyaan Anda disukai!");
        } 
        // Jika UNLIKE → tidak ada notifikasi
    }

    // Log kegiatan
    mysqli_query($conn,
        "INSERT INTO activity_log (user_id, action, detail, created_at)
         VALUES ($user_id, 'like_question', 'question:$question_id,state:$result', NOW())"
    );

    header("Location: index.php?page=view_question&id=" . $question_id);
}

/**
 * Compatibility wrapper for controller:
 * controller menggunakan vote_answer_save($conn, $answer_id, $user_id, $value)
 * sedangkan model punya vote_toggle_answer($conn, $user_id, $answer_id, $value)
 */

function vote_answer_save($conn, $answer_id, $user_id, $value){
    // pastikan parameter urutannya cocok dengan vote_toggle_answer
    if (!function_exists('vote_toggle_answer')) {
        // fallback: implement simple save if vote_toggle_answer tidak ada
        $answer_id = (int)$answer_id; $user_id = (int)$user_id; $value = (int)$value;
        $r = mysqli_query($conn, "SELECT * FROM vote WHERE answer_id=$answer_id AND user_id=$user_id LIMIT 1");
        if (mysqli_num_rows($r)){
            return mysqli_query($conn, "UPDATE vote SET vote_value=$value, created_at='".date('Y-m-d H:i:s')."' WHERE answer_id=$answer_id AND user_id=$user_id");
        } else {
            return mysqli_query($conn, "INSERT INTO vote (answer_id,user_id,vote_value,created_at) VALUES ($answer_id,$user_id,$value,'".date('Y-m-d H:i:s')."')");
        }
    }
    // call the existing model function with its parameter order
    return vote_toggle_answer($conn, $user_id, $answer_id, $value);
}

?>
