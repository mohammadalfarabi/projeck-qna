<?php
$question = $data['question'];
$answers  = $data['answers'];
$comments = $data['comments'];
$user_id  = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($question['title']) ?></title>

<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/components.css">
<link rel="stylesheet" href="assets/css/pages/answer.css">

</head>
<body>

<div class="answer-container">

    <!-- QUESTION CARD -->
    <div class="question-card-full">

        <h1 class="question-title"><?= htmlspecialchars($question['title']) ?></h1>

        <div class="question-meta">
            <?php $u = user_get_by_id($conn, $question['user_id']); ?>
            <img src="<?= user_get_photo_url($conn, $u['user_id']) ?>" class="q-user-img">
            <div>
                <b><?= $u['name'] ?></b><br>
                <small><?= $question['created_at'] ?></small>
            </div>
        </div>

        <p class="question-body"><?= nl2br(htmlspecialchars($question['content'])) ?></p>

        <!-- QUESTION STATS -->
        <div class="question-stats">
            <span>👍 <?= question_like_count($conn, $question['question_id']) ?></span>
            <span>💬 <?= comment_count_question($conn, $question['question_id']) ?></span>
            <span>👁 <?= $question['views'] ?></span>
        </div>

        <!-- VOTE BUTTON -->
        <a href="index.php?page=vote_question&id=<?= $question['question_id'] ?>" class="btn-like">
            👍 Like
        </a>

        <!-- COMMENT FORM QUESTION -->
        <div class="comment-form">
            <form action="index.php?page=comment_save_question" method="POST">
                <input type="hidden" name="question_id" value="<?= $question['question_id'] ?>">

                <textarea name="comment" placeholder="Tulis komentar..." required></textarea>
                
                <button class="btn-primary w-100">Kirim Komentar</button>
            </form>
        </div>

        <!-- COMMENTS LIST -->
        <div class="comment-list">
            <?php
            $cQ = mysqli_query($conn, "SELECT * FROM comment WHERE question_id=" . $question['question_id']);
            while ($c = mysqli_fetch_assoc($cQ)):
                $cu = user_get_by_id($conn, $c['user_id']);
            ?>
                <div class="comment-item">
                    <img src="<?= user_get_photo_url($conn, $cu['user_id']) ?>" class="comment-img">

                    <div>
                        <b><?= $cu['name'] ?></b>
                        <p><?= nl2br(htmlspecialchars($c['comment_text'])) ?></p>
                        <small><?= $c['created_at'] ?></small>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    </div>




    <!-- ANSWER SECTION -->
    <h2 class="section-title">💡 Jawaban</h2>

    <div class="answer-list">

        <?php if (empty($answers)): ?>
            
            <div class="empty">Belum ada jawaban.</div>

        <?php else: foreach($answers as $a): ?>

            <?php $au = user_get_by_id($conn, $a['user_id']); ?>

            <div class="answer-card">

                <div class="answer-meta">
                    <img src="<?= user_get_photo_url($conn, $au['user_id']) ?>" class="a-user-img">
                    <div>
                        <b><?= $au['name'] ?></b><br>
                        <small><?= $a['created_at'] ?></small>
                    </div>
                </div>

                <p class="answer-body"><?= nl2br(htmlspecialchars($a['answer_text'])) ?></p>

                <div class="answer-stats">
                    <span>👍 <?= answer_like_count($conn, $a['answer_id']) ?></span>
                    <span>💬 <?= comment_count_answer($conn, $a['answer_id']) ?></span>
                </div>

                <!-- LIKE ANSWER -->
                <a href="index.php?page=vote_answer&id=<?= $a['answer_id'] ?>" class="btn-like">
                    👍 Like Jawaban
                </a>

                <!-- COMMENT FORM ANSWER -->
                <div class="comment-form">
                    <form action="index.php?page=comment_save_answer" method="POST">
                        <input type="hidden" name="answer_id" value="<?= $a['answer_id'] ?>">
                        <textarea name="comment" placeholder="Komentar pada jawaban..." required></textarea>
                        <button class="btn-primary w-100">Kirim Komentar</button>
                    </form>
                </div>

                <!-- ANSWER COMMENTS -->
                <div class="comment-list">
                    <?php
                    $cA = mysqli_query($conn, "SELECT * FROM comment WHERE answer_id=" . $a['answer_id']);
                    while ($c = mysqli_fetch_assoc($cA)):
                        $cu = user_get_by_id($conn, $c['user_id']);
                    ?>
                        <div class="comment-item">
                            <img src="<?= user_get_photo_url($conn, $cu['user_id']) ?>" class="comment-img">
                            <div>
                                <b><?= $cu['name'] ?></b>
                                <p><?= nl2br(htmlspecialchars($c['comment_text'])) ?></p>
                                <small><?= $c['created_at'] ?></small>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

            </div>

        <?php endforeach; endif; ?>

    </div>




    <!-- ADD NEW ANSWER -->
    <h2 class="section-title">➕ Tambah Jawaban</h2>

    <div class="answer-form-card">

        <form action="index.php?page=answer_save" method="POST" class="answer-form">

            <input type="hidden" name="question_id" value="<?= $question['question_id'] ?>">

            <textarea name="answer" placeholder="Tulis jawaban terbaik kamu..." rows="4" required></textarea>

            <button class="btn-primary w-100 mt-10">Kirim Jawaban</button>

        </form>

    </div>

</div>

</body>
</html>
