<?php
$question = $data['question'];
$tags = $data['tags'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Pertanyaan</title>

<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/components.css">
<link rel="stylesheet" href="assets/css/pages/question_edit.css">

</head>
<body>

<div class="question-edit-container">

    <h1 class="page-title">✏️ Edit Pertanyaan</h1>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="edit-card">

        <form action="index.php?page=question_update" method="POST" class="edit-form">

            <input type="hidden" name="question_id" value="<?= $question['question_id'] ?>">

            <!-- JUDUL -->
            <div class="input-group">
                <label>Judul Pertanyaan</label>
                <input type="text" name="title" value="<?= htmlspecialchars($question['title']) ?>" required>
            </div>

            <!-- ISI -->
            <div class="input-group">
                <label>Isi Pertanyaan</label>
                <textarea name="content" rows="6" required><?= htmlspecialchars($question['content']) ?></textarea>
            </div>

            <!-- TAG -->
            <div class="input-group">
                <label>Tag</label>
                <select name="tag_id" required>
                    <?php while($t = mysqli_fetch_assoc($tags)): ?>
                        <option value="<?= $t['tag_id'] ?>"
                            <?= $t['tag_id'] == $question['tag_id'] ? 'selected' : '' ?>>
                            <?= $t['tag_name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button class="btn-primary w-100 mt-20">Simpan Perubahan</button>

        </form>

    </div>

</div>

</body>
</html>
