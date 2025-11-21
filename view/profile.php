<?php
$user_id = $_SESSION['user_id'];
$me = user_get_by_id($conn, $user_id);

// foto profil
$photo = user_get_photo($conn, $user_id);
$photo_url = $photo ? "uploads/" . $photo['photo'] : "assets/img/default.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Profil Saya</title>

<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/components.css">
<link rel="stylesheet" href="assets/css/pages/profile.css">

</head>
<body>

<div class="profile-container">

    <h1 class="page-title">👤 Profil Saya</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>


    <!-- PROFILE CARD -->
    <div class="profile-card">

        <div class="profile-left">

            <!-- Foto Profil -->
            <img src="<?= $photo_url ?>" class="profile-photo" alt="Profile">

            <form action="index.php?page=upload_photo" method="POST" enctype="multipart/form-data">
                <input type="file" name="photo" class="input-file" required>
                <button class="btn-primary w-100">Upload Foto</button>
            </form>
        </div>


        <!-- FORM DATA PERSONAL -->
        <div class="profile-right">

            <form action="index.php?page=update_profile" method="POST" class="profile-form">

                <div class="input-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" value="<?= $me['name'] ?>" required>
                </div>

                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= $me['email'] ?>" required>
                </div>

                <div class="input-group">
                    <label>Sekolah</label>
                    <input type="text" value="<?= $me['school_name'] ?>" disabled>
                </div>

                <div class="input-group">
                    <label>Role</label>
                    <input type="text" value="<?= ucfirst($me['role']) ?>" disabled>
                </div>

                <button class="btn-primary mt-20">Update Profil</button>

            </form>

        </div>

    </div>


    <!-- GANTI PASSWORD -->
    <div class="password-card">

        <h2>🔐 Ganti Password</h2>

        <form action="index.php?page=update_password" method="POST" class="password-form">

            <div class="input-group">
                <label>Password Lama</label>
                <input type="password" name="old_password" required>
            </div>

            <div class="input-group">
                <label>Password Baru</label>
                <input type="password" name="new_password" required>
            </div>

            <button class="btn-primary w-100 mt-10">Update Password</button>
        </form>

    </div>

</div>

</body>
</html>
