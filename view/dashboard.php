<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
    body {
        background-color: #F2D1D1 !important;  /* warna background baru */
    }
         /* --- AREA DASHBOARD UTAMA --- */
.dashboard {
    background: #FFE6E6;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-top: 20px;
    font-family: 'Segoe UI', sans-serif;
}


/* --- JUDUL UTAMA --- */
.dashboard h2 {
    font-size: 28px;
    margin-bottom: 20px;
    color: #000000ff;
    font-weight: 600;
}

/* --- FORM FILTER TAG --- */
.dashboard select {
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #9CAFAA;
    background: #C6DCE4;
    outline: none;
    transition: all .2s ease;
    color: #333;
}

.dashboard select:focus {
    border-color: #D6A99D;
    box-shadow: 0 0 5px rgba(214,169,157,0.4);
}

/* --- FORM AJUKAN PERTANYAAN --- */
.ask-question {
    background: #C6DCE4;
    padding: 20px;
    margin-bottom: 25px;
    border-radius: 10px;
    border-left: 5px solid #D6A99D; /* accent utama */
}

.ask-question h3 {
    margin-bottom: 15px;
    color: #D6A99D;
}

.ask-question-form input[type="text"],
.ask-question-form textarea,
.ask-question-form select {
    width: 100%;
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #9CAFAA;
    margin-bottom: 15px;
    font-size: 15px;
    transition: 0.2s;
    background: #FBF3D5;
}

.ask-question-form input:focus,
.ask-question-form textarea:focus,
.ask-question-form select:focus {
    border-color: #D6A99D;
    box-shadow: 0 0 6px rgba(214,169,157,0.4);
}

/* BUTTON */
.ask-question-form button {
    background: #D6A99D;
    color: white;
    padding: 10px 18px;
    font-size: 15px;
    border: none;
    border-radius: 7px;
    cursor: pointer;
    transition: 0.2s;
}

.ask-question-form button:hover {
    background: #c0897f;
}

/* --- LIST PERTANYAAN --- */
.questions-list h3 {
    margin-bottom: 15px;
    border-bottom: 2px solid #D6A99D;
    padding-bottom: 5px;
    color: #000000ff;
}

.question-item {
    padding: 15px 18px;
    background: #DAEAF1;
    border-radius: 8px;
    border: 1px solid #c9e7f4ff;
    margin-bottom: 15px;
    transition: 0.2s;
}

.question-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.question-item h4 a {
    text-decoration: none;
    font-size: 20px;
    color: #D6A99D;
    font-weight: 600;
    transition: 0.2s;
}

.question-item h4 a:hover {
    color: #b77f74;
}

.question-item p {
    margin: 10px 0;
    color: #000000ff;
}

/* --- TANGGAL & USER --- */
.question-item small b {
    color: #050404ff;
}

/* --- PAGINATION --- */
.pagination a {
    padding: 7px 12px;
    border-radius: 6px;
    border: 1px solid #cbe8f5ff;
    margin: 0 3px;
    text-decoration: none;
    color: #333;
    background: #FBF3D5;
    font-size: 14px;
    transition: 0.2s;
}

.pagination a:hover {
    background: #D6A99D;
    color: white;
    border-color: #D6A99D;
}

/* Halaman aktif */
.pagination a[style*="font-weight: bold"] {
    background: #D6A99D !important;
    color: white !important;
    border-color: #D6A99D !important;
}
.question-item {
    background-color: #DAEAF1 !important;  /* ganti warna biru */
    border-radius: 10px;
    padding: 15px;
}

    </style>
</head>
<body>
    
</body>
</html>


<?php
include 'header.php';
?>

<div class="container dashboard">

    <h2>Forum Q&A</h2>

    
    <div class="ask-question">
        <h3>Submit a Question</h3>
        <form action="../controller/forum_controller.php" method="POST" class="form-group ask-question-form">
            <input type="hidden" name="action" value="create_question">
            
            <div class="form-group">
                <label>Question Title</label>
                <input type="text" name="title" required>
            </div>
            
            <div class="form-group">
                <label>Select a Tag:</label>
                <select name="tag_id" required>
                    <option value="">-- Select a Tag --</option>
                    
                    <?php
                    mysqli_data_seek($tags, 0); // reset pointer result
                    while ($tag = mysqli_fetch_assoc($tags)) {
                        echo "<option value='" . htmlspecialchars($tag['tag_id']) . "'>" . htmlspecialchars($tag['tag_name']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Question Content:</label>
                <textarea name="body" rows="5" required></textarea>
            </div>
            
            <button type="submit">Send a Question</button>
        </form>
    </div>

    <form method="GET" style="margin-bottom: 20px;" class="form-group">
        <label>Filter Tag:</label>
        <select name="tag_id" onchange="this.form.page.value=1; this.form.submit()">
            <option value="">-- Select a Tag --</option>
            <?php mysqli_data_seek($tags, 0);
            while ($tag = mysqli_fetch_assoc($tags)) { ?>
                <option value="<?= htmlspecialchars($tag['tag_id']); ?>"
                    <?= ($tag_filter == $tag['tag_id']) ? "selected" : ""; ?>>
                    <?= htmlspecialchars($tag['tag_name']); ?>
                </option>
            <?php } ?>
        </select>
        <input type="hidden" name="page" value="1">
    </form>
    
    <div class="questions-list">
        <h3>New Question <?= $tag_filter ? "(Disaring berdasarkan tag)" : "" ?></h3>

        <?php if (mysqli_num_rows($questions) == 0): ?>
            <p>There are No Question With This Tag.</p>
        <?php endif; ?>

        <?php while ($question = mysqli_fetch_assoc($questions)): ?>
            <div class="question-item question">
                <h4>
                    <a href="../controller/forum_controller.php?action=question_detail&id=<?= $question['question_id']; ?>">
                        <?= htmlspecialchars($question['title']); ?>
                    </a>
                </h4>

                <p><?= nl2br(htmlspecialchars(substr($question['body'], 0, 200))); ?>...</p>

                <small>
                    Asked By: <b><?= htmlspecialchars($question['user_name']); ?></b>
                    On <?= date("d M Y H:i", strtotime($question['created_at'])); ?>
                </small>
                <br>
                <small>Tag: <b><?= htmlspecialchars($question['tags']); ?></b></small>
            </div>
        <?php endwhile; ?>
    </div>


    <!-- Pagination Tabs -->
    <?php if ($total_pages > 1): ?>
        <div class="pagination" style="margin-top: 20px;">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="index.php?controller=dashboard&action=index&page=<?= $i ?><?= $tag_filter ? '&tag_id=' . urlencode($tag_filter) : '' ?><?= $search_title ? '&search_title=' . urlencode($search_title) : '' ?>"
                    style="padding: 5px 10px; margin: 0 3px; border: 1px solid #ccc; text-decoration: none; <?= ($i == $page) ? 'font-weight: bold; background-color: #eee;' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

</div>

<script>
    function showTab(tabName) {
        var questionsTab = document.getElementById('tabQuestions');
        var historyTab = document.getElementById('tabHistory');
        var questionsBtn = document.getElementById('tabQuestionsBtn');
        var historyBtn = document.getElementById('tabHistoryBtn');

        if (tabName === 'questions') {
            questionsTab.style.display = 'block';
            historyTab.style.display = 'none';
            questionsBtn.setAttribute('aria-selected', 'true');
            historyBtn.setAttribute('aria-selected', 'false');
        } else {
            questionsTab.style.display = 'none';
            historyTab.style.display = 'block';
            questionsBtn.setAttribute('aria-selected', 'false');
            historyBtn.setAttribute('aria-selected', 'true');
        }
    }
</script>

<?php include 'footer.php'; ?>
