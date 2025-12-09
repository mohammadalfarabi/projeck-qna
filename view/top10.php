<?php
include 'header.php';
?>
<style>
    body {
        background-color: #F2D1D1;
        /* warna background baru */
    }

    /* ===============================
   GLOBAL CONTAINER
================================ */
    .container {
        background: #FFE6E6;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        margin-top: 25px;
        font-family: 'Poppins', sans-serif;
    }

    /* ===============================
   HEADER TITLE
================================ */
    .top10-page h2 {
        font-size: 30px;
        font-weight: 700;
        margin-bottom: 25px;
        color: #2a2a2a;
    }

    /* ===============================
   TOGGLE LINKS
================================ */
    .toggle-links {
        margin-bottom: 20px;
        font-size: 15px;
        background: #C6DCE4;
        padding: 10px 15px;
        border-radius: 10px;
        display: inline-block;
    }

    .toggle-links a {
        color: #003d66;
        font-weight: 600;
        text-decoration: none;
        padding: 6px 12px;
        border-radius: 8px;
        transition: 0.25s ease;
    }

    .toggle-links a:hover {
        background: #DAEAF1;
    }

    /* ===============================
   LIST ITEMS
================================ */
    .question-item,
    .answer-item {
        background: #DAEAF1;
        border-radius: 16px;
        padding: 22px;
        margin-bottom: 20px;
        border-left: 6px solid #D6A99D;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .question-item h4,
    .answer-item h4 {
        font-size: 20px;
        color: #1f1f1f;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .question-item p,
    .answer-item p {
        font-size: 15px;
        color: #4d4d4d;
        margin-bottom: 12px;
    }

    small {
        font-size: 13px;
        color: #666;
    }

    /* ===============================
   ORDER LIST
================================ */
    ol {
        padding-left: 25px;
    }

    ol li {
        margin-bottom: 12px;
    }

    /* Hover effect */
    .question-item:hover,
    .answer-item:hover {
        transform: translateY(-3px);
        transition: .25s;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    }
</style>

<div class="container top10-page">
    <h2>Top 10 <?php echo ($display_type === 'answers') ? 'Best Answers' : 'Best Questions'; ?></h2>


    <div class="toggle-links">
        <a href="index.php?controller=top10&action=index&type=questions"
            <?php if ($display_type === 'questions') echo 'style="font-weight: bold; text-decoration: underline;"'; ?>>
            Best Questions
        </a> |
        <a href="index.php?controller=top10&action=index&type=answers"
            <?php if ($display_type === 'answers') echo 'style="font-weight: bold; text-decoration: underline;"'; ?>>
            Best Answers
        </a>
    </div>

    <?php if ($display_type === 'answers'): ?>
        <?php if (empty($top_answers)): ?>
            <p>No answers have been voted on in your school.</p>
        <?php else: ?>
            <ol>
                <?php foreach ($top_answers as $answer): ?>
                    <li>
                        <div class="answer-item answer">
                            <h4><?php echo substr($answer['question_title'], 0, 50); ?>...</h4>
                            <p><?php echo substr($answer['body'], 0, 200); ?>...</p>
                            <small>Answered by: <?php echo htmlspecialchars($answer['user_name']); ?> | Votes: <?php echo $answer['total_votes'] ?: 0; ?></small>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    <?php else: ?>
        <?php if (empty($top_questions)): ?>
            <p>No questions have been commented on in your school.</p>
        <?php else: ?>
            <ol>
                <?php foreach ($top_questions as $question): ?>
                    <li>
                        <div class="question-item question">
                            <h4><?php echo substr($question['title'], 0, 50); ?>...</h4>
                            <p><?php echo substr($question['body'], 0, 200); ?>...</p>
                            <small>Asked by: <?php echo htmlspecialchars($question['user_name']); ?> | Comments: <?php echo $question['total_comments'] ?: 0; ?></small>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    <?php endif; ?>

    <?php include 'footer.php'; ?>