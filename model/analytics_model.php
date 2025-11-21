<?php
// model/analytics_model.php

function analytics_totals($conn){
    $totals = [];
    $t = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM user"));
    $totals['users'] = (int)$t['c'];
    $t = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM question"));
    $totals['questions'] = (int)$t['c'];
    $t = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM answer"));
    $totals['answers'] = (int)$t['c'];
    return $totals;
}

// activity last N days (group by date)
function analytics_activity_by_day($conn, $days=7){
    $days = (int)$days;
    $q = "SELECT DATE(created_at) AS d, COUNT(*) AS c FROM activity_log WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL $days DAY) GROUP BY DATE(created_at) ORDER BY d ASC";
    return mysqli_query($conn, $q);
}

// top users by points
function analytics_top_users($conn, $limit=10){
    $limit = (int)$limit;
    return mysqli_query($conn, "SELECT user_id, name, points FROM user ORDER BY points DESC LIMIT $limit");
}

// Total user, pertanyaan, jawaban
function analytics_get_summary($conn){
    $data = [];

    $t1 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM user"));
    $t2 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM question"));
    $t3 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM answer"));

    $data['total_users'] = $t1['total'];
    $data['total_questions'] = $t2['total'];
    $data['total_answers'] = $t3['total'];

    return $data;
}


// Aktivitas 7 hari terakhir
function analytics_daily_activity($conn){
    $rows = [];

    $q = mysqli_query($conn, "
        SELECT DATE(created_at) AS day, COUNT(*) AS total
        FROM activity_log
        WHERE created_at >= DATE(NOW()) - INTERVAL 7 DAY
        GROUP BY DATE(created_at)
        ORDER BY day ASC
    ");

    while($r = mysqli_fetch_assoc($q)){
        $rows[] = $r;
    }

    return $rows;
}


// Statistik berdasarkan sekolah
function analytics_school_stats($conn){
    return mysqli_query($conn, "
        SELECT s.school_name,
               (SELECT COUNT(*) FROM user u WHERE u.school_id = s.school_id) AS total_users,
               (SELECT COUNT(*) FROM question q JOIN user u ON u.user_id=q.user_id WHERE u.school_id = s.school_id) AS total_questions,
               (SELECT COUNT(*) FROM answer a JOIN user u ON u.user_id=a.user_id WHERE u.school_id = s.school_id) AS total_answers
        FROM school s
        ORDER BY s.school_name ASC
    ");
}


// Aktivitas terbaru
function analytics_recent_activity($conn){
    return mysqli_query($conn, "
        SELECT a.*, u.name
        FROM activity_log a
        JOIN user u ON u.user_id = a.user_id
        ORDER BY a.created_at DESC
        LIMIT 20
    ");
}

?>
