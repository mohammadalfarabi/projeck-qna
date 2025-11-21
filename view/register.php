<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - Mini QnA</title>

<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/components.css">
<link rel="stylesheet" href="assets/css/pages/register.css">

</head>
<body class="auth-body">

<div class="auth-container">

    <div class="auth-card">

        <h2 class="auth-title">Daftar Akun Baru ✨</h2>
        <p class="auth-subtitle">Bergabunglah dengan komunitas QnA</p>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert error"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="index.php?page=do_register" method="POST" class="auth-form">

            <div class="input-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" placeholder="Nama anda" required>
            </div>

            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="email@example.com" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="input-group">
                <label>Sekolah</label>
                <select name="school_id" required>
                    <option value="">-- Pilih Sekolah --</option>
                    <?php
                        global $conn;
                        $schools = mysqli_query($conn, "SELECT * FROM school ORDER BY school_name ASC");
                        while($s = mysqli_fetch_assoc($schools)):
                    ?>
                    <option value="<?= $s['school_id'] ?>"><?= $s['school_name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button class="btn-primary auth-btn">Register</button>

        </form>

        <p class="auth-bottom">
            Sudah punya akun?
            <a href="index.php?page=login">Login</a>
        </p>

    </div>

</div>

</body>
</html>
