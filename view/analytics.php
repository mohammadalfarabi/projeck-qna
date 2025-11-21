<?php
// required variables from controller:
// $summary, $school_stats, $recent, $chart_labels, $chart_values
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Analytics Dashboard</title>

<!-- CSS PREMIUM (TERPISAH) -->
<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/components.css">
<link rel="stylesheet" href="assets/css/pages/analytics.css">

</head>
<body>

<div class="analytics-container">

    <h1 class="page-title">📊 Analytics Dashboard</h1>

    <!-- STATISTIC CARDS -->
    <div class="stats-grid">
        <div class="card stat-card">
            <div class="stat-title">Total Users</div>
            <div class="stat-value"><?= $summary['total_users'] ?></div>
        </div>

        <div class="card stat-card">
            <div class="stat-title">Total Questions</div>
            <div class="stat-value"><?= $summary['total_questions'] ?></div>
        </div>

        <div class="card stat-card">
            <div class="stat-title">Total Answers</div>
            <div class="stat-value"><?= $summary['total_answers'] ?></div>
        </div>
    </div>

    <!-- GRAPH ACTIVITY -->
    <div class="card analytics-graph">
        <div class="section-title">📈 Aktivitas 7 Hari Terakhir</div>
        <canvas id="activityChart"></canvas>
    </div>

    <!-- SCHOOL STATS -->
    <div class="section-title">🏫 Statistik Sekolah</div>
    <div class="card">
        <table class="modern-table">
            <tr>
                <th>Nama Sekolah</th>
                <th>Total User</th>
                <th>Total Pertanyaan</th>
                <th>Total Jawaban</th>
            </tr>

            <?php while($s = mysqli_fetch_assoc($school_stats)): ?>
            <tr>
                <td><?= $s['school_name'] ?></td>
                <td><?= $s['total_users'] ?></td>
                <td><?= $s['total_questions'] ?></td>
                <td><?= $s['total_answers'] ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- RECENT ACTIVITIES -->
    <div class="section-title">🕒 Aktivitas Terbaru</div>
    <div class="card">
        <table class="modern-table">
            <tr>
                <th>User</th>
                <th>Action</th>
                <th>Detail</th>
                <th>Waktu</th>
            </tr>

            <?php while($a = mysqli_fetch_assoc($recent)): ?>
            <tr>
                <td><?= $a['name'] ?></td>
                <td><?= $a['action'] ?></td>
                <td><?= $a['detail'] ?></td>
                <td><?= $a['created_at'] ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

</div>


<script>
// DATA UNTUK GRAFIK
const labels = <?= json_encode($chart_labels) ?>;
const values = <?= json_encode($chart_values) ?>;

// GRAFIK <CANVAS>
const canvas = document.getElementById("activityChart");
const ctx = canvas.getContext("2d");

let progress = 0;
const speed = 3;

function draw(){
    ctx.clearRect(0,0,canvas.width,canvas.height);

    ctx.lineWidth = 3;
    ctx.strokeStyle = "#4FC3F7";
    ctx.beginPath();

    for(let i = 0; i < values.length; i++){
        let x = (i * (canvas.width / (values.length - 1)));
        let y = canvas.height - (values[i] * 8 * (progress / 100));

        if(i === 0) ctx.moveTo(x, y);
        else ctx.lineTo(x, y);
    }
    ctx.stroke();

    ctx.fillStyle = "#ccc";
    ctx.font = "12px Poppins";

    for(let i = 0; i < labels.length; i++){
        let x = (i * (canvas.width / (labels.length - 1)));
        ctx.fillText(labels[i], x, canvas.height - 5);
    }

    if(progress < 100){
        progress += speed;
        requestAnimationFrame(draw);
    }
}

draw();
</script>

</body>
</html>
