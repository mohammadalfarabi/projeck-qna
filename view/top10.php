<?php
$user_id = $_SESSION['user_id'];
$me = user_get_by_id($conn, $user_id);
$school_id = $me['school_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Top 10 - Mini QnA</title>

<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/components.css">
<link rel="stylesheet" href="assets/css/pages/top10.css">

</head>
<body>

<div class="top10-container">

    <h1 class="page-title">🔥 Top 10 dari Sekolah Anda</h1>
    <p class="subtitle">
        Berdasarkan aktivitas dari siswa sekolah: <b><?= $me['school_name'] ?></b>
    </p>


    <!-- TOP 10 QUESTIONS -->
    <div class="section-block">
        <h2 class="section-title">🏆 Top 10 Pertanyaan Terpopuler</h2>

        <?php if (mysqli_num_rows($top_questions) == 0): ?>
            <div class="empty">Belum ada pertanyaan dari sekolah Anda.</div>
        <?php else: ?>

        <table class="modern-table">
            <tr>
                <th>Judul</th>
                <th>Pembuat</th>
                <th>Komentar</th>
                <th>Like</th>
                <th>Waktu</th>
            </tr>

            <?php while($q = mysqli_fetch_assoc($top_questions)): ?>
                <?php
                $userQ = user_get_by_id($conn, $q['user_id']);
                ?>
                <tr>
                    <td>
                        <a href="index.php?page=view_question&id=<?= $q['question_id'] ?>" class="link-title">
                            <?= htmlspecialchars($q['title']) ?>
                        </a>
                    </td>
                    <td><?= $userQ['name'] ?></td>
                    <td><?= $q['total_comments'] ?></td>
                    <td><?= $q['likes'] ?></td>
                    <td><?= $q['created_at'] ?></td>
                </tr>
            <?php endwhile; ?>

        </table>

        <?php endif; ?>
    </div>




    <!-- TOP 10 ANSWERS -->
    <div class="section-block">
        <h2 class="section-title">💡 Top 10 Jawaban Terbaik</h2>

        <?php if (mysqli_num_rows($top_answers) == 0): ?>
            <div class="empty">Belum ada jawaban dari sekolah Anda.</div>
        <?php else: ?>

        <table class="modern-table">
            <tr>
                <th>Jawaban</th>
                <th>Pertanyaan</th>
                <th>Penjawab</th>
                <th>Like</th>
                <th>Waktu</th>
            </tr>

            <?php while($a = mysqli_fetch_assoc($top_answers)): ?>
                <?php
                $u = user_get_by_id($conn, $a['user_id']);
                $q = question_get_by_id($conn, $a['question_id']);
                ?>
                <tr>
                    <td><?= substr($a['answer_text'], 0, 80) ?>...</td>
                    <td>
                        <a href="index.php?page=view_question&id=<?= $q['question_id'] ?>" class="link-title">
                            <?= htmlspecialchars($q['title']) ?>
                        </a>
                    </td>
                    <td><?= $u['name'] ?></td>
                    <td><?= $a['likes'] ?></td>
                    <td><?= $a['created_at'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <?php endif; ?>
    </div>


</div>

</body>
</html>
