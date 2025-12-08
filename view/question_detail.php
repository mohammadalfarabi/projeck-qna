<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background-color: #F2D1D1 !important;
            /* warna background baru */
        }

        /* Container dasar */
        .container.question-detail {
            background: #DAEAF1;
            padding: 25px;
            border-radius: 15px;
            max-width: 900px;
            margin: 30px auto;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            font-family: "Poppins", sans-serif;
        }

        /* Notification */
        .notification {
            padding: 12px 18px;
            margin-bottom: 15px;
            border-radius: 10px;
            font-weight: 600;
            text-align: center;
            display: none;
        }

        .notification.success {
            background: #C6DCE4;
            color: #2a4d69;
        }

        .notification.error {
            background: #F2D1D1;
            color: #8b1a1a;
        }

        /* Bagian pertanyaan */
        .question {
            background: #FFE6E6;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .question h2 {
            margin-bottom: 8px;
            color: #3a3a3a;
        }

        .question-meta {
            font-size: 0.9rem;
            color: #555;
        }

        /* Komentar */
        .comment-section,
        .answers-section {
            background: #C6DCE4;
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
        }

        .comment-list,
        .answer-list {
            list-style: none;
            padding: 0;
        }

        .comment {
            background: #FFE6E6;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 10px;
        }

        .comment-meta {
            font-size: 0.8rem;
            margin-top: 5px;
            color: #666;
        }

        /* Form komentar */
        .comment-form textarea,
        .answer-form-submit textarea {
            width: 100%;
            height: 90px;
            padding: 10px;
            border-radius: 10px;
            border: 2px solid #DAEAF1;
            outline: none;
            transition: 0.2s;
            background: white;
        }

        .comment-form textarea:focus,
        .answer-form-submit textarea:focus {
            border-color: #C6DCE4;
        }

        /* Tombol */
        button {
            background: #F2D1D1;
            padding: 10px 16px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.2s;
            font-weight: 600;
        }

        button:hover {
            background: #FFE6E6;
        }

        /* Jawaban */
        .answer {
            background: white;
            padding: 18px;
            border-radius: 15px;
            margin-bottom: 15px;
            border-left: 5px solid #C6DCE4;
        }

        .answer-meta {
            font-size: 0.9rem;
            color: #444;
            margin-bottom: 10px;
        }

        /* Vote */
        .vote-section {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        .vote-btn {
            border-radius: 8px;
            padding: 4px 8px;
        }

        /* Form Jawaban */
        .answer-form {
            background: #DAEAF1;
            padding: 20px;
            border-radius: 15px;
            margin-top: 20px;
        }
    </style>

</head>

<body>
    <?php
    include 'header.php';
    ?>

    <div class="container question-detail">
        <div id="notification" class="notification" style="display: none;"></div>

        <div class="question">
            <h2><?php echo htmlspecialchars($question['title']); ?></h2>
            <p><?php echo nl2br(htmlspecialchars($question['body'])); ?></p>
            <div class="question-meta">Ditanya oleh: <?php echo htmlspecialchars($question['user_name']); ?></div>
        </div>

        <div class="comment-section">
            <h3>Komentar pada pertanyaan</h3>
            <?php if (empty($question_comments)) : ?>
                <p>Belum ada komentar pada pertanyaan ini.</p>
            <?php else : ?>
                <ul class="comment-list">
                    <?php foreach ($question_comments as $comment) : ?>
                        <li class="comment">
                            <strong><?php echo htmlspecialchars($comment['user_name']); ?>:</strong>
                            <?php echo nl2br(htmlspecialchars($comment['body'])); ?>
                            <div class="comment-meta"><?php echo $comment['created_at']; ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>


            <!-- Form to add comment on question -->
            <h4>Tambahkan Komentar pada Pertanyaan</h4>
            <form method="POST" action="index.php?controller=forum&action=create_comment" class="comment-form">
                <input type="hidden" name="action" value="create_comment">
                <input type="hidden" name="question_id" value="<?php echo $question_id; ?>">
                <textarea name="body" required placeholder="Tulis komentar Anda di sini"></textarea><br>
                <button type="submit">Kirim Komentar</button>
            </form>
        </div>

        <hr>

        <div class="answers-section">
            <h3>Jawaban</h3>
            <?php if (empty($answers)) : ?>
                <p>Belum ada jawaban untuk pertanyaan ini.</p>
            <?php else : ?>
                <ul class="answer-list">
                    <?php foreach ($answers as $answer) : ?>
                        <li class="answer">
                            <p><?php echo nl2br(htmlspecialchars($answer['body'])); ?></p>
                            <div class="answer-meta">Jawaban dari: <?php echo htmlspecialchars($answer['user_name']); ?></div>
                            <div class="vote-section">
                                Votes: <span class="vote-count" id="vote-count-<?php echo $answer['answer_id']; ?>"><?php echo $answer['total_votes']; ?></span>
                                <button type="button" class="vote-btn" data-answer-id="<?php echo $answer['answer_id']; ?>" data-vote-value="1" title="Upvote" style="background:none; border:none; cursor:pointer; font-size:1.2rem;">👍</button>
                            </div>

                            <div class="answer-comments">
                                <h4>Komentar pada jawaban ini</h4>
                                <?php if (empty($answer_comments[$answer['answer_id']])) : ?>
                                    <p>Belum ada komentar pada jawaban ini.</p>
                                <?php else : ?>
                                    <ul class="comment-list">
                                        <?php foreach ($answer_comments[$answer['answer_id']] as $comment) : ?>
                                            <li class="comment">
                                                <strong><?php echo htmlspecialchars($comment['user_name']); ?>:</strong>
                                                <?php echo nl2br(htmlspecialchars($comment['body'])); ?>
                                                <div class="comment-meta"><?php echo $comment['created_at']; ?></div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>


                                <!-- Form to add comment on answer -->
                                <h5>Tambahkan Komentar pada Jawaban</h5>
                                <form method="POST" action="index.php?controller=forum&action=create_comment" class="comment-form">
                                    <input type="hidden" name="action" value="create_comment">
                                    <input type="hidden" name="question_id" value="<?php echo $question_id; ?>">
                                    <input type="hidden" name="answer_id" value="<?php echo $answer['answer_id']; ?>">
                                    <textarea name="body" required placeholder="Tulis komentar Anda di sini"></textarea><br>
                                    <button type="submit">Kirim Komentar</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>


        <div class="answer-form">
            <h3>Berikan Jawaban</h3>
            <form method="POST" action="index.php?controller=forum&action=create_answer" class="answer-form-submit">
                <input type="hidden" name="action" value="create_answer">
                <input type="hidden" name="question_id" value="<?php echo $question_id; ?>">
                <textarea name="body" required placeholder="Tulis jawaban Anda di sini"></textarea><br>
                <button type="submit">Kirim Jawaban</button>
            </form>
        </div>

    </div>

    <script>
        const questionId = <?php echo $question_id; ?>;

        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = 'notification ' + type;
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }


        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing forms...');

            const voteButtons = document.querySelectorAll('.vote-btn');
            console.log('Found vote buttons:', voteButtons.length);

            voteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const answerId = this.getAttribute('data-answer-id');
                    const voteValue = this.getAttribute('data-vote-value');
                    console.log('Vote button clicked:', answerId, voteValue);

                    // Disable button during request
                    this.disabled = true;

                    fetch('index.php?controller=vote&action=vote', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: new URLSearchParams({
                                answer_id: answerId,
                                vote_value: voteValue
                            })
                        })
                        .then(response => {
                            console.log('Vote response status:', response.status);
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.text();
                        })
                        .then(text => {
                            console.log('Vote response text:', text);
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Invalid JSON response:', text);
                                throw new Error('Invalid JSON response from server');
                            }
                        })
                        .then(data => {
                            console.log('Vote data:', data);
                            if (data.success) {
                                const voteCountElement = document.getElementById('vote-count-' + answerId);
                                if (voteCountElement) {
                                    voteCountElement.textContent = data.new_vote_count || '0';
                                }
                                showNotification(data.message || 'Vote berhasil', 'success');
                            } else {
                                showNotification(data.message || 'Gagal melakukan vote', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Vote error:', error);
                            showNotification('Terjadi kesalahan saat melakukan vote: ' + error.message, 'error');
                        })
                        .finally(() => {
                            this.disabled = false;
                        });
                });
            });

            const commentForms = document.querySelectorAll('.comment-form');
            console.log('Found comment forms:', commentForms.length);

            commentForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    console.log('Comment form submitted');

                    const formData = new FormData(this);
                    const submitButton = this.querySelector('button[type="submit"]');
                    
                    // Disable button during request
                    if (submitButton) submitButton.disabled = true;

                    fetch('index.php?controller=forum&action=create_comment', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        })
                        .then(response => {
                            console.log('Comment response status:', response.status);
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.text();
                        })
                        .then(text => {
                            console.log('Comment response text:', text);
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Invalid JSON response:', text);
                                throw new Error('Invalid JSON response from server');
                            }
                        })
                        .then(data => {
                            console.log('Comment data:', data);
                            if (data.success) {
                                showNotification(data.message || 'Komentar berhasil dikirim', 'success');
                                this.reset();

                                if (data.comment) {
                                    const commentContainer = this.closest('.comment-section, .answer-comments');
                                    let commentList = commentContainer.querySelector('.comment-list');
                                    if (!commentList) {
                                        const noCommentsMsg = commentContainer.querySelector('p');
                                        if (noCommentsMsg) noCommentsMsg.remove();

                                        commentList = document.createElement('ul');
                                        commentList.className = 'comment-list';
                                        commentContainer.insertBefore(commentList, this);
                                    }
                                    const newComment = document.createElement('li');
                                    newComment.className = 'comment';
                                    newComment.innerHTML = `
                            <strong>${data.comment.user_name}:</strong>
                            ${data.comment.body.replace(/\n/g, '<br>')}
                            <div class="comment-meta">${data.comment.created_at}</div>
                        `;
                                    commentList.insertBefore(newComment, commentList.firstChild);
                                }
                            } else {
                                showNotification(data.message || 'Gagal mengirim komentar', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Comment error:', error);
                            showNotification('Terjadi kesalahan saat mengirim komentar: ' + error.message, 'error');
                        })
                        .finally(() => {
                            if (submitButton) submitButton.disabled = false;
                        });
                });
            });

            const answerForm = document.querySelector('.answer-form-submit');
            console.log('Found answer form:', !!answerForm);

            if (answerForm) {
                answerForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    console.log('Answer form submitted');

                    const formData = new FormData(this);
                    const submitButton = this.querySelector('button[type="submit"]');
                    
                    // Validate required fields
                    const body = formData.get('body');
                    if (!body || body.trim() === '') {
                        showNotification('Jawaban tidak boleh kosong', 'error');
                        return;
                    }

                    // Disable button during request
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.textContent = 'Mengirim...';
                    }

                    fetch('index.php?controller=forum&action=create_answer', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        })
                        .then(response => {
                            console.log('Answer response status:', response.status);
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.text();
                        })
                        .then(text => {
                            console.log('Answer response text:', text);
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Invalid JSON response:', text);
                                throw new Error('Invalid JSON response from server');
                            }
                        })
                        .then(data => {
                            console.log('Answer data:', data);
                            if (data.success) {
                                showNotification(data.message || 'Jawaban berhasil dikirim', 'success');
                                this.reset();

                                if (data.answer) {
                                    const answersSection = document.querySelector('.answers-section');
                                    let answerList = answersSection.querySelector('.answer-list');

                                    if (!answerList) {
                                        const noAnswersMsg = answersSection.querySelector('p');
                                        if (noAnswersMsg) noAnswersMsg.remove();

                                        answerList = document.createElement('ul');
                                        answerList.className = 'answer-list';
                                        answersSection.insertBefore(answerList, this.closest('.answer-form'));
                                    }

                                    const newAnswer = document.createElement('li');
                                    newAnswer.className = 'answer';
                                    newAnswer.innerHTML = `
                            <p>${data.answer.body.replace(/\n/g, '<br>')}</p>
                            <div class="answer-meta">Jawaban dari: ${data.answer.user_name}</div>
                            <div class="vote-section">
                                Votes: <span class="vote-count" id="vote-count-${data.answer.answer_id}">${data.answer.total_votes}</span>
                                <button type="button" class="vote-btn" data-answer-id="${data.answer.answer_id}" data-vote-value="1" title="Upvote" style="background:none; border:none; cursor:pointer; font-size:1.2rem;">👍</button>
                            </div>
                            <div class="answer-comments">
                                <h4>Komentar pada jawaban ini</h4>
                                <p>Belum ada komentar pada jawaban ini.</p>
                                <h5>Tambahkan Komentar pada Jawaban</h5>
                                <form method="POST" action="index.php?controller=forum&action=create_comment" class="comment-form">
                                    <input type="hidden" name="action" value="create_comment">
                                    <input type="hidden" name="question_id" value="${questionId}">
                                    <input type="hidden" name="answer_id" value="${data.answer.answer_id}">
                                    <textarea name="body" required placeholder="Tulis komentar Anda di sini"></textarea><br>
                                    <button type="submit">Kirim Komentar</button>
                                </form>
                            </div>
                        `;
                                    answerList.insertBefore(newAnswer, answerList.firstChild);

                                    // Re-bind event listeners for new elements
                                    const newVoteButton = newAnswer.querySelector('.vote-btn');
                                    const newCommentForm = newAnswer.querySelector('.comment-form');
                                    
                                    if (newVoteButton) {
                                        newVoteButton.addEventListener('click', function() {
                                            const answerId = this.getAttribute('data-answer-id');
                                            const voteValue = this.getAttribute('data-vote-value');
                                            console.log('New vote button clicked:', answerId, voteValue);
                                            this.disabled = true;
                                            fetch('index.php?controller=vote&action=vote', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/x-www-form-urlencoded',
                                                        'X-Requested-With': 'XMLHttpRequest'
                                                    },
                                                    body: new URLSearchParams({
                                                        answer_id: answerId,
                                                        vote_value: voteValue
                                                    })
                                                })
                                                .then(response => response.text())
                                                .then(text => JSON.parse(text))
                                                .then(data => {
                                                    if (data.success) {
                                                        const voteCountElement = document.getElementById('vote-count-' + answerId);
                                                        if (voteCountElement) {
                                                            voteCountElement.textContent = data.new_vote_count || '0';
                                                        }
                                                        showNotification(data.message || 'Vote berhasil', 'success');
                                                    } else {
                                                        showNotification(data.message || 'Gagal melakukan vote', 'error');
                                                    }
                                                })
                                                .catch(error => {
                                                    console.error('Vote error:', error);
                                                    showNotification('Terjadi kesalahan saat melakukan vote: ' + error.message, 'error');
                                                })
                                                .finally(() => {
                                                    this.disabled = false;
                                                });
                                        });
                                    }

                                    if (newCommentForm) {
                                        newCommentForm.addEventListener('submit', function(e) {
                                            e.preventDefault();
                                            console.log('New comment form submitted');

                                            const formData = new FormData(this);
                                            const submitButton = this.querySelector('button[type="submit"]');
                                            
                                            if (submitButton) submitButton.disabled = true;

                                            fetch('index.php?controller=forum&action=create_comment', {
                                                    method: 'POST',
                                                    headers: {
                                                        'X-Requested-With': 'XMLHttpRequest'
                                                    },
                                                    body: formData
                                                })
                                                .then(response => response.text())
                                                .then(text => JSON.parse(text))
                                                .then(data => {
                                                    if (data.success) {
                                                        showNotification(data.message || 'Komentar berhasil dikirim', 'success');
                                                        this.reset();

                                                        if (data.comment) {
                                                            const commentContainer = this.closest('.answer-comments');
                                                            let commentList = commentContainer.querySelector('.comment-list');
                                                            if (!commentList) {
                                                                const noCommentsMsg = commentContainer.querySelector('p');
                                                                if (noCommentsMsg) noCommentsMsg.remove();

                                                                commentList = document.createElement('ul');
                                                                commentList.className = 'comment-list';
                                                                commentContainer.insertBefore(commentList, this);
                                                            }
                                                            const newComment = document.createElement('li');
                                                            newComment.className = 'comment';
                                                            newComment.innerHTML = `
                                                    <strong>${data.comment.user_name}:</strong>
                                                    ${data.comment.body.replace(/\n/g, '<br>')}
                                                    <div class="comment-meta">${data.comment.created_at}</div>
                                                `;
                                                            commentList.insertBefore(newComment, commentList.firstChild);
                                                        }
                                                    } else {
                                                        showNotification(data.message || 'Gagal mengirim komentar', 'error');
                                                    }
                                                })
                                                .catch(error => {
                                                    console.error('Comment error:', error);
                                                    showNotification('Terjadi kesalahan saat mengirim komentar: ' + error.message, 'error');
                                                })
                                                .finally(() => {
                                                    if (submitButton) submitButton.disabled = false;
                                                });
                                        });
                                    }
                                }
                            } else {
                                showNotification(data.message || 'Gagal mengirim jawaban', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Answer error:', error);
                            showNotification('Terjadi kesalahan saat mengirim jawaban: ' + error.message, 'error');
                        })
                        .finally(() => {
                            if (submitButton) {
                                submitButton.disabled = false;
                                submitButton.textContent = 'Kirim Jawaban';
                            }
                        });
                });
            }
        });
    </script>

    <?php include 'footer.php'; ?>

</body>

</html>