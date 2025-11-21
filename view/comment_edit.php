<?php
$comment = $data['comment'];
$question = $data['question'];
$answer = isset($data['answer']) ? $data['answer'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Komentar</title>

<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/components.css">
<link rel="stylesheet" href="assets/css/pages/comment_edit.css">

</head>
<body>

<div class="comment-edit-container">

    <h1 class="page-title">✏️ Edit Komentar</h1>

    <!-- Preview context -->
    <div class="comment-context-card">
        <?php if ($answer): ?>
            <h2>Komentar pada Jawaban</h2>
            <p><?= nl2br(htmlspecialchars($answer['answer_text'])) ?></p>
        <?php else: ?>
            <h2>Komentar pada Pertanyaan</h2>
            <p><?= nl2br(htmlspecialchars($question['content'])) ?></p>
        <?php endif; ?>
    </div>

    <div class="edit-card">

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form action="index.php?page=comment_update" method="POST" class="edit-form">

            <input type="hidden" name="comment_id" value="<?= $comment['comment_id'] ?>">
            <input type="hidden" name="question_id" value="<?= $question['question_id'] ?>">
            <?php if ($answer): ?>
                <input type="hidden" name="answer_id" value="<?= $answer['answer_id'] ?>">
            <?php endif; ?>

            <div class="input-group">
                <label>Edit Komentar</label>
                <textarea name="comment_text" rows="5" required><?= htmlspecialchars($comment['comment_text']) ?></textarea>
            </div>

            <button class="btn-primary w-100 mt-20">Simpan Perubahan</button>

        </form>

    </div>

</div>

</body>
</html>
