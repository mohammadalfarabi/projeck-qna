<?php
$user_id = $_SESSION['user_id'];

// Ambil data user
$me = user_get_by_id($conn, $user_id);

// Foto profil
$photo = user_get_photo($conn, $user_id);
$photo_url = $photo ? "uploads/profile/" . $photo['photo'] : "assets/img/default.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard - Mini QnA</title>

<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/components.css">
<link rel="stylesheet" href="assets/css/pages/dashboard.css">

</head>
<body>

<div class="layout">

    <!-- LEFT SIDEBAR -->
    <aside class="sidebar-left">

        <div class="profile-card">
            <img src="<?= $photo_url ?>" class="profile-img" alt="Profile">
            <h3><?= $me['name'] ?></h3>
            <p><?= $me['email'] ?></p>
            <span class="badge"><?= ucfirst($me['role']) ?></span>
        </div>

        <nav class="menu">
            <a href="index.php?page=dashboard" class="menu-item active">🏠 Forum QnA</a>
            <a href="index.php?page=top10" class="menu-item">🔥 Top 10</a>
            <a href="index.php?page=profile" class="menu-item">👤 Profil</a>

            <?php if ($_SESSION['role'] == 'teacher'): ?>
                <a href="index.php?page=analytics" class="menu-item">📊 Analytics Admin</a>
            <?php endif; ?>

            <a href="index.php?page=logout" class="menu-item logout">🚪 Logout</a>
        </nav>

    </aside>




    <!-- MAIN CONTENT -->
    <main class="main-content">

        <div class="main-header">
            <h1>Forum Tanya Jawab 📚</h1>

            <a href="index.php?page=ask" class="btn-primary">+ Buat Pertanyaan</a>
        </div>

        <!-- SEARCH -->
        <form class="search-bar" method="GET" action="index.php">
            <input type="hidden" name="page" value="dashboard">
            <input type="text" name="q" placeholder="Cari pertanyaan..."
                   value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
            <button class="btn-search">Cari</button>
        </form>

        <!-- TAG FILTER -->
        <div class="tag-list">
            <?php while($t = mysqli_fetch_assoc($tags)): ?>
                <a href="index.php?page=dashboard&tag=<?= $t['tag_id'] ?>"
                   class="tag <?= (isset($_GET['tag']) && $_GET['tag']==$t['tag_id']) ? 'active' : '' ?>">
                    #<?= $t['tag_name'] ?>
                </a>
            <?php endwhile; ?>
        </div>

        <!-- QUESTION LIST -->
        <div class="question-list">

            <?php if (empty($questions)): ?>
                
                <div class="empty">Tidak ada pertanyaan ditemukan.</div>

            <?php else: foreach($questions as $q): ?>
                
                <?php
                $userQ = user_get_by_id($conn, $q['user_id']);
                $likes = question_like_count($conn, $q['question_id']);
                ?>

                <div class="question-card">

                    <div class="question-meta">
                        <img src="<?= user_get_photo_url($conn, $userQ['user_id']) ?>" class="q-user-img">
                        <div>
                            <b><?= $userQ['name'] ?></b><br>
                            <small><?= $q['created_at'] ?></small>
                        </div>
                    </div>

                    <a class="question-title" href="index.php?page=view_question&id=<?= $q['question_id'] ?>">
                        <?= htmlspecialchars($q['title']) ?>
                    </a>

                    <div class="question-stats">
                        <span>💬 <?= comment_count_question($conn, $q['question_id']) ?> komentar</span>
                        <span>👍 <?= $likes ?> like</span>
                        <span>👁 <?= $q['views'] ?> views</span>
                    </div>

                </div>

            <?php endforeach; endif; ?>

        </div>


        <!-- PAGINATION -->
        <div class="pagination">
            <?php for ($i=1; $i <= $pages; $i++): ?>
                <a class="page-btn <?= ($i==$page?'active':'') ?>"
                   href="index.php?page=dashboard&page_num=<?= $i ?><?= isset($_GET['q'])?'&q='.$_GET['q']:'' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>

    </main>




    <!-- RIGHT SIDEBAR (HISTORY) -->
    <aside class="sidebar-right">

        <h3>Riwayat Aktivitas</h3>

        <div class="history-list">
            <?php
            $his = mysqli_query($conn, "
                SELECT * FROM activity_log 
                WHERE user_id = $user_id 
                ORDER BY created_at DESC 
                LIMIT 10
            ");
            ?>

            <?php while($h = mysqli_fetch_assoc($his)): ?>
                <div class="history-item">
                    <b><?= $h['action'] ?></b>
                    <small><?= $h['created_at'] ?></small>
                    <div><?= $h['detail'] ?></div>
                </div>
            <?php endwhile; ?>

        </div>

    </aside>

</div>

</body>
</html>
