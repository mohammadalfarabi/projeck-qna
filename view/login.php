<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Mini QnA</title>

<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/components.css">
<link rel="stylesheet" href="assets/css/pages/login.css">

</head>
<body class="auth-body">

<div class="auth-container">

    <div class="auth-card">

        <h2 class="auth-title">Selamat Datang! 👋</h2>
        <p class="auth-subtitle">Silakan masuk untuk melanjutkan</p>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert error"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form action="index.php?page=do_login" method="POST" class="auth-form">

            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="email@example.com" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <button class="btn-primary auth-btn">Login</button>

        </form>

        <p class="auth-bottom">
            Belum punya akun?
            <a href="index.php?page=register">Daftar sekarang</a>
        </p>

    </div>

</div>

</body>
</html>


