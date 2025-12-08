<style>
/* ==========================================
   NAVBAR MAIN
========================================== */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #D6A99D;
    padding: 14px 22px;
    border-bottom: 2px solid #b98b7f;
    font-family: 'Poppins', sans-serif;
}

/* ==========================================
   LEFT SECTION
========================================== */
.nav-left {
    display: flex;
    align-items: center;
    gap: 35px;
}

/* ===== PROFILE CARD ===== */
.profile {
    background: #F2D1D1;
    padding: 10px 16px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    min-width: 200px;
}

.profile h3 {
    margin: 0 0 4px 0;
    font-size: 1.15rem;
    font-weight: 700;
    color: #222;
}

.profile p {
    margin: 2px 0;
    font-size: 0.85rem;
    color: #444;
}

/* ===== NAV LINKS ===== */
.nav-links {
    display: flex;
    align-items: center;
    gap: 22px;
    white-space: nowrap;
}

.nav-links a {
    color: #222;
    text-decoration: none;
    font-weight: 700;
    padding: 7px 12px;
    border-radius: 6px;
    transition: 0.2s ease;
    font-size: 0.95rem;
}

.nav-links a:hover {
    background-color: #F2D1D1;
    color: #a16161;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

/* ==========================================
   RIGHT SECTION - QUESTION HISTORY
========================================== */
.nav-right {
    max-width: 330px;
}

details {
    background: #ffffff;
    border: 1px solid #bbb;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    padding: 10px 15px;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.95rem;
    user-select: none;
}

details[open] {
    box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}

summary {
    list-style: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

summary::-webkit-details-marker {
    display: none;
}

/* ===== DROPDOWN CONTENT ===== */
.question-history-dropdown {
    margin-top: 10px;
    max-height: 180px;
    overflow-y: auto;
}

.question-history-item {
    display: block;
    padding: 8px 12px;
    margin-bottom: 6px;
    background-color: #F2D1D1;
    border-radius: 6px;
    text-decoration: none;
    color: #222;
    font-size: 0.9rem;
    box-shadow: inset 0 -1px 0 #ddd;
    transition: 0.2s ease;
    position: relative;
}

.question-history-item:hover {
    background-color: #DAEAF1;
    color: #1a73e8;
}

.question-history-item small {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.75rem;
    color: #555;
}
</style>

<div class="navbar">
    <div class="nav-left">
        <div class="profile">
            <h3><?php echo htmlspecialchars($user['name']); ?></h3>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
            <p><?php echo htmlspecialchars($user['school_name']); ?></p>
        </div>

        <div class="nav-links">

            <a href="index.php?controller=dashboard&action=index" title="Dashboard">Forum QnA</a>
            <a href="index.php?controller=top10&action=index" title="Top 10 Questions">Top 10</a>
            <?php if ($_SESSION['role'] == 'teacher'): ?>
                <a href="index.php?controller=teacher&action=dashboard" title="Kelola User">Manage User</a>
            <?php endif; ?>
            <a href="index.php?controller=auth&action=logout" title="Logout">Logout</a>
        </div>
    </div>

    <div class="nav-right">
        <details>
            <summary>
                <span>Your Question History</span>
                <span style="font-size: 0.8em;">▼</span>
            </summary>

            <div class="question-history-dropdown">
                <?php if(empty($user_questions)): ?>
                    <p>No Question History.</p>
                <?php else: ?>
                    <?php foreach ($user_questions as $question): ?>

                        <a href="index.php?controller=forum&action=question_detail&id=<?php echo $question['question_id']; ?>" 
                           class="question-history-item">
                            <?php echo htmlspecialchars(mb_strimwidth($question['title'], 0, 50, "...")); ?>
                            <small><?php echo date('d M Y', strtotime($question['created_at'])); ?></small>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </details>
    </div>
</div>
