<?php
$answer = $data['answer'];
$question = $data['question'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Jawaban</title>

<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/components.css">
<link rel="stylesheet" href="assets/css/pages/answer_edit.css">

</head>
<body>

<div class="answer-edit-container">

    <h1 class="page-title">✏️ Edit Jawaban Anda</h1>

    <!-- Info Pertanyaan -->
    <div class="question-preview">
        <h2><?= htmlspecialchars($question['title']) ?></h2>
        <p><?= nl2br(htmlspecialchars($question['content'])) ?></p>
        <small>Dibuat: <?= $question['created_at'] ?></small>
    </div>

    <div class="edit-card">

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form action="index.php?page=answer_update" method="POST" class="edit-form">

            <input type="hidden" name="answer_id" value="<?= $answer['answer_id'] ?>">
            <input type="hidden" name="question_id" value="<?= $question['question_id'] ?>">

            <div class="input-group">
                <label>Edit Jawaban</label>
                <textarea name="answer_text" rows="6" required><?= htmlspecialchars($answer['answer_text']) ?></textarea>
            </div>

            <button class="btn-primary w-100 mt-20">Simpan Perubahan</button>

        </form>

    </div>

</div>

</body>
</html>
