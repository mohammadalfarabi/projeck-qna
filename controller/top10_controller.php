<?php
// controller/top10_controller.php

require_once 'model/question_model.php';
require_once 'model/answer_model.php';
require_once 'model/comment_model.php';
require_once 'model/user_model.php';

function ensure_login_top(){
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?page=login");
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| HALAMAN TOP 10 UTAMA
|--------------------------------------------------------------------------
*/
function show_top10(){
    global $conn;
    ensure_login_top();

    $user_id = $_SESSION['user_id'];

    // Ambil sekolah user
    $me = user_get_by_id($conn, $user_id);
    $school_id = (int)$me['school_id'];

    // Top 10 pertanyaan paling banyak komentar (per sekolah)
    $top_questions = mysqli_query($conn, "
        SELECT q.*, 
               (SELECT COUNT(*) FROM comment c WHERE c.question_id = q.question_id) AS total_comments
        FROM question q
        JOIN user u ON u.user_id = q.user_id
        WHERE u.school_id = $school_id
        ORDER BY total_comments DESC
        LIMIT 10
    ");

    // Top 10 jawaban paling banyak vote (per sekolah)
    $top_answers = mysqli_query($conn, "
        SELECT a.*, 
               (SELECT SUM(vote_value) FROM vote WHERE vote.answer_id = a.answer_id) AS vote_total
        FROM answer a
        JOIN user u ON u.user_id = a.user_id
        WHERE u.school_id = $school_id
        ORDER BY vote_total DESC
        LIMIT 10
    ");

    // Leaderboard user berdasarkan poin
    $leaderboard_users = mysqli_query($conn, "
        SELECT u.*, s.school_name
        FROM user u
        JOIN school s ON u.school_id = s.school_id
        WHERE u.school_id = $school_id
        ORDER BY u.points DESC
        LIMIT 10
    ");

    // Leaderboard sekolah (rata-rata poin tertinggi)
    $leaderboard_schools = mysqli_query($conn, "
        SELECT s.school_id, s.school_name,
               AVG(u.points) AS avg_points
        FROM school s
        JOIN user u ON u.school_id = s.school_id
        GROUP BY s.school_id
        ORDER BY avg_points DESC
        LIMIT 10
    ");

    require 'view/top10.php';
}

/*
|--------------------------------------------------------------------------
| BADGE (BRONZE / SILVER / GOLD / PLATINUM)
|--------------------------------------------------------------------------
| Dipakai di leaderboard
*/
function get_user_badge($points){

    if ($points >= 600){
        return "Platinum";
    }
    else if ($points >= 300){
        return "Gold";
    }
    else if ($points >= 100){
        return "Silver";
    }
    return "Bronze";
}

?>
