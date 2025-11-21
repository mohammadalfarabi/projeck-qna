<?php
// controller/analytics_controller.php

require_once 'model/analytics_model.php';
require_once 'model/user_model.php';

function ensure_admin(){
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
        header("Location: index.php?page=dashboard");
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| SHOW ANALYTICS DASHBOARD
|--------------------------------------------------------------------------
*/
function show_analytics(){
    global $conn;
    ensure_admin();

    // Total user, pertanyaan, jawaban
    $summary = analytics_get_summary($conn);

    // Aktivitas 7 hari terakhir
    $daily = analytics_daily_activity($conn);

    // Statistik berdasarkan sekolah
    $school_stats = analytics_school_stats($conn);

    // Aktivitas terbaru 20
    $recent = analytics_recent_activity($conn);

    // Prepare data untuk grafik <canvas>
    $chart_labels = [];
    $chart_values = [];

    foreach ($daily as $row) {
        $chart_labels[] = $row['day'];
        $chart_values[] = $row['total'];
    }

    require 'view/analytics.php';
}
?>
