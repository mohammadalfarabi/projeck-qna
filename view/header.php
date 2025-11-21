<?php require_once 'model/user_model.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Mini QnA</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <div class="topbar">
  <h1>MINI QnA</h1>

  <button id="darkToggle" class="darkmode-btn">
    🌙
  </button>
</div>

  <div class="layout">
    <aside class="leftnav" aria-label="Main navigation">
      <?php if(isset($_SESSION['user_id'])): $u = user_get_by_id($GLOBALS['conn'], $_SESSION['user_id']); ?>
        <div class="profile-card" role="region" aria-label="User profile">
          <div style="font-weight:600"><?php echo htmlspecialchars($u['name']); ?></div>
          <div style="font-size:13px;color:var(--muted)"><?php echo htmlspecialchars($u['email']); ?></div>
          <div style="font-size:13px;color:var(--muted)"><?php echo htmlspecialchars($u['school_name']); ?></div>
        </div>

        <div style="margin-top:12px">
          <a class="btn" href="index.php?page=dashboard">Forum QnA</a><br>
          <a class="smallbtn" href="index.php?page=ask">Tanyakan</a><br>
          <a class="smallbtn" href="index.php?page=top10">Top 10</a><br>
          <a class="smallbtn" href="index.php?page=history">Riwayat</a><br>
          <?php if(isset($_SESSION['role']) && $_SESSION['role']=='teacher'): ?>
            <a class="smallbtn" href="index.php?page=admin_users">Admin</a><br>
          <?php endif; ?>
          <div style="margin-top:10px">
            <a class="smallbtn" href="index.php?page=profile">Profil</a>
            <a class="smallbtn" href="index.php?page=logout">Keluar</a>
          </div>
        </div>
      <?php else: ?>
        <a class="btn" href="index.php?page=login">Login</a>
        <a class="btn" href="index.php?page=register">Register</a>
      <?php endif; ?>
    </aside>

    <main class="main">
