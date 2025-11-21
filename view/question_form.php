<?php
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Buat Pertanyaan Baru</title>

<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/components.css">
<link rel="stylesheet" href="assets/css/pages/question.css">

</head>
<body>

<div class="question-container">

    <h1 class="page-title">💬 Buat Pertanyaan Baru</h1>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="question-card">

        <form action="index.php?page=question_save" method="POST" class="question-form">

            <!-- JUDUL -->
            <div class="input-group">
                <label>Judul Pertanyaan</label>
                <input type="text" name="title" placeholder="Tulis judul pertanyaan Anda..." required>
            </div>

            <!-- ISI -->
            <div class="input-group">
                <label>Isi Pertanyaan</label>
                <textarea name="content" rows="6" placeholder="Jelaskan dengan lengkap..." required></textarea>
            </div>

            <!-- TAG -->
            <div class="input-group">
                <label>Pilih Tag</label>
                <select name="tag_id" required>
                    <option value="">-- Pilih tag --</option>
                    <?php while($t = mysqli_fetch_assoc($tags)): ?>
                        <option value="<?= $t['tag_id'] ?>"><?= $t['tag_name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- submit -->
            <button class="btn-primary w-100 mt-20">Kirim Pertanyaan</button>

        </form>

    </div>

</div>

</body>
</html>
