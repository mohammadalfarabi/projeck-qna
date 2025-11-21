<?php
// controller/forum_controller.php
require_once 'model/question_model.php';
require_once 'model/answer_model.php';
require_once 'model/comment_model.php';
require_once 'model/user_model.php';
require_once 'model/tag_model.php';
require_once 'model/notify_model.php';



// -------------------------------
// Helper: check login
// -------------------------------
function ensure_login(){
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?page=login');
        exit;
    }
}

// -------------------------------
// SHOW DASHBOARD (with pagination, search, tag filter)
// -------------------------------
// URL params:
//  - q = search query
//  - tag = tag_id (optional)
//  - page = page number (pagination)
//  - per = items per page (optional)
function show_dashboard(){
    global $conn;
    ensure_login();

    $q = isset($_GET['q']) ? trim($_GET['q']) : null;
    $tag = isset($_GET['tag']) ? (int)$_GET['tag'] : null;
    $page = isset($_GET['page_num']) ? max(1,(int)$_GET['page_num']) : 1;
    $per = isset($_GET['per']) ? (int)$_GET['per'] : 10;
    $offset = ($page - 1) * $per;

    // If tag filter active, fetch questions by tag
    if ($tag){
        $questions = tag_get_questions($conn, $tag);
        // tag_get_questions returns full list (no pagination) in model; we'll slice manually
        // Convert to array for easy slicing
        $all = [];
        while($row = mysqli_fetch_assoc($questions)) $all[] = $row;
        $total = count($all);
        $paged = array_slice($all, $offset, $per);
        // Convert back to mysqli-like result: simple workaround is to use $paged as array in view
        $questions_result = $paged;
    } else {
        // Use model with limit/offset
        $questions_result = [];
        $res = question_get_all($conn, $q, $per, $offset);
        while($r = mysqli_fetch_assoc($res)) $questions_result[] = $r;

        // total count for pagination
        if ($q) {
            $countR = mysqli_query($conn, "SELECT COUNT(*) AS c FROM question WHERE title LIKE '%".mysqli_real_escape_string($conn,$q)."%' OR body LIKE '%".mysqli_real_escape_string($conn,$q)."%'");
            $countRow = mysqli_fetch_assoc($countR);
            $total = (int)$countRow['c'];
        } else {
            $countR = mysqli_query($conn, "SELECT COUNT(*) AS c FROM question");
            $countRow = mysqli_fetch_assoc($countR);
            $total = (int)$countRow['c'];
        }
    }

    // get list tags for sidebar
    $tags = tag_list_all($conn);

    // prepare pagination meta
    $pages = ceil($total / $per);

    // Pass variables to view (dashboard.php expects $questions as mysqli result earlier;
    // here we keep compatibility by providing $questions as array)
    $questions = $questions_result;

    require 'view/dashboard.php';
}

// -------------------------------
// SHOW FORM - ASK QUESTION
// -------------------------------
function show_question_form(){
    global $conn;
    ensure_login();
    // load available tags for helper/autocomplete
    $all_tags = tag_list_all($conn);
    require 'view/question_form.php';
}

// -------------------------------
// POST NEW QUESTION (attach tags if provided)
// -------------------------------
function post_question(){
    global $conn;
    ensure_login();

    $user_id = (int)$_SESSION['user_id'];
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $body  = isset($_POST['body']) ? trim($_POST['body']) : '';
    $tags_input = isset($_POST['tags']) ? trim($_POST['tags']) : ''; // comma separated string

    if ($title == "" || $body == "") {
        $_SESSION['error'] = "Judul dan isi pertanyaan wajib diisi.";
        header('Location: index.php?page=ask');
        exit;
    }

    // Create question and get id
    $qid = question_create($conn, $user_id, $title, $body);
    if (!$qid) {
        $_SESSION['error'] = "Gagal membuat pertanyaan.";
        header('Location: index.php?page=ask');
        exit;
    }

    // Attach tags
    if ($tags_input != "") {
        $parts = array_map('trim', explode(',', $tags_input));
        foreach ($parts as $t) {
            if ($t == "") continue;
            $tag_id = tag_get_or_create($conn, $t);
            if ($tag_id) question_attach_tag($conn, $qid, $tag_id);
        }
    }

    // activity log + points
    user_add_points($conn, $user_id, 5); // +5 points for creating question
    mysqli_query($conn, "INSERT INTO activity_log (user_id, action, detail, created_at) VALUES ($user_id, 'question_create', 'question_id:$qid', NOW())");

    header('Location: index.php?page=view_question&id='.$qid);
}

// -------------------------------
// VIEW SINGLE QUESTION + ANSWERS + COMMENTS
// -------------------------------
function show_question(){
    global $conn;
    ensure_login();

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) { header('Location: index.php?page=dashboard'); exit; }

    // fetch question, answers, comments, tags
    $q = question_get_by_id($conn, $id);
    if (!$q) {
        $_SESSION['error'] = "Pertanyaan tidak ditemukan.";
        header('Location: index.php?page=dashboard');
        exit;
    }

    // increment views
    question_increment_views($conn, $id);

    $answers = answer_get_by_question($conn, $id);
    $comments_q = comment_get_by_question($conn, $id);
    $tags = question_get_tags($conn, $id);

    require 'view/answer_form.php';
}

// -------------------------------
// POST ANSWER
// -------------------------------
function post_answer(){
    global $conn;
    ensure_login();

    $user_id = (int)$_SESSION['user_id'];
    $question_id = isset($_POST['question_id']) ? (int)$_POST['question_id'] : 0;
    $body = isset($_POST['body']) ? trim($_POST['body']) : '';

    if ($question_id <= 0 || $body == "") {
        $_SESSION['error'] = "Jawaban tidak boleh kosong.";
        header("Location: index.php?page=view_question&id=".$question_id);
        exit;
    }

    $aid = answer_create($conn, $question_id, $user_id, $body);
    if ($aid) {
        // notify question owner that someone answered
        $q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT user_id, title FROM question WHERE question_id=$question_id LIMIT 1"));
        if ($q && (int)$q['user_id'] !== $user_id) {
            $msg = "Pertanyaan Anda \"" . mysqli_real_escape_string($conn, substr($q['title'],0,100)) . "\" mendapat jawaban.";
            notify_create($conn, (int)$q['user_id'], $msg);
        }
        mysqli_query($conn, "INSERT INTO activity_log (user_id, action, detail, created_at) VALUES ($user_id, 'answer_create', 'answer_id:$aid', NOW())");
    }

    header("Location: index.php?page=view_question&id=".$question_id);
}

// -------------------------------
// POST COMMENT (on question or answer)
// -------------------------------
function post_comment(){
    global $conn;
    ensure_login();

    $user_id = (int)$_SESSION['user_id'];
    $question_id = isset($_POST['question_id']) ? (int)$_POST['question_id'] : null;
    $answer_id = isset($_POST['answer_id']) ? (int)$_POST['answer_id'] : null;
    $body = isset($_POST['body']) ? trim($_POST['body']) : '';

    if ($body == "") {
        $_SESSION['error'] = "Komentar tidak boleh kosong.";
        if ($question_id) header("Location: index.php?page=view_question&id=".$question_id);
        else header("Location: index.php?page=dashboard");
        exit;
    }

    $ok = comment_create($conn, $user_id, $question_id, $answer_id, $body);
    if ($ok) {
        // notify owner (question owner or answer owner)
        if ($answer_id) {
            $owner = mysqli_fetch_assoc(mysqli_query($conn, "SELECT user_id, body FROM answer WHERE answer_id=$answer_id LIMIT 1"));
            if ($owner && (int)$owner['user_id'] !== $user_id) {
                notify_create($conn, (int)$owner['user_id'], "Jawaban Anda mendapat komentar.");
            }
            mysqli_query($conn, "INSERT INTO activity_log (user_id, action, detail, created_at) VALUES ($user_id, 'comment_create', 'answer:$answer_id', NOW())");
        } elseif ($question_id) {
            $owner = mysqli_fetch_assoc(mysqli_query($conn, "SELECT user_id FROM question WHERE question_id=$question_id LIMIT 1"));
            if ($owner && (int)$owner['user_id'] !== $user_id) {
                notify_create($conn, (int)$owner['user_id'], "Pertanyaan Anda mendapat komentar.");
            }
            mysqli_query($conn, "INSERT INTO activity_log (user_id, action, detail, created_at) VALUES ($user_id, 'comment_create', 'question:$question_id', NOW())");
        }
    }

    if ($question_id) header("Location: index.php?page=view_question&id=".$question_id);
    else header("Location: index.php?page=dashboard");
}

// -------------------------------
// EDIT QUESTION (show form + save handled here)
// -------------------------------
function show_question_edit(){
    global $conn;
    ensure_login();

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $q = question_get_by_id($conn, $id);
    if (!$q) { $_SESSION['error']="Pertanyaan tidak ditemukan."; header('Location: index.php?page=dashboard'); exit; }

    // only owner or teacher can edit
    if ($q['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] != 'teacher') {
        $_SESSION['error']="Anda tidak berhak mengedit pertanyaan ini.";
        header('Location: index.php?page=view_question&id='.$id);
        exit;
    }

    $tags = question_get_tags($conn, $id);
    require 'view/question_edit.php';
}

function do_question_edit(){
    global $conn;
    ensure_login();

    $qid = isset($_POST['question_id']) ? (int)$_POST['question_id'] : 0;
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $body = isset($_POST['body']) ? trim($_POST['body']) : '';
    $tags_input = isset($_POST['tags']) ? trim($_POST['tags']) : '';

    $qinfo = question_get_by_id($conn, $qid);
    if (!$qinfo) { $_SESSION['error']="Pertanyaan tidak ditemukan."; header('Location: index.php?page=dashboard'); exit; }

    if ($qinfo['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] != 'teacher') {
        $_SESSION['error']="Anda tidak berhak mengedit pertanyaan ini.";
        header('Location: index.php?page=view_question&id='.$qid); exit;
    }

    question_edit($conn, $qid, $_SESSION['user_id'], $title, $body);

    // update tags: remove old and attach new
    question_detach_all_tags($conn, $qid);
    if ($tags_input != "") {
        $parts = array_map('trim', explode(',', $tags_input));
        foreach ($parts as $t) {
            if ($t == "") continue;
            $tid = tag_get_or_create($conn, $t);
            if ($tid) question_attach_tag($conn, $qid, $tid);
        }
    }

    mysqli_query($conn, "INSERT INTO activity_log (user_id, action, detail, created_at) VALUES (".$_SESSION['user_id'].", 'question_edit', 'question:$qid', NOW())");

    header('Location: index.php?page=view_question&id='.$qid);
}

// -------------------------------
// DELETE QUESTION
// -------------------------------
function do_question_delete(){
    global $conn;
    ensure_login();

    $qid = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $qinfo = question_get_by_id($conn, $qid);
    if (!$qinfo) { $_SESSION['error']="Pertanyaan tidak ditemukan."; header('Location: index.php?page=dashboard'); exit; }

    if ($qinfo['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] != 'teacher') {
        $_SESSION['error']="Anda tidak berhak menghapus pertanyaan ini.";
        header('Location: index.php?page=view_question&id='.$qid); exit;
    }

    question_delete($conn, $qid);
    mysqli_query($conn, "INSERT INTO activity_log (user_id, action, detail, created_at) VALUES (".$_SESSION['user_id'].", 'question_delete', 'question:$qid', NOW())");

    header('Location: index.php?page=dashboard');
}

// -------------------------------
// EDIT ANSWER
// -------------------------------
function show_answer_edit(){
    global $conn;
    ensure_login();

    $aid = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $res = mysqli_query($conn, "SELECT a.*, q.title FROM answer a JOIN question q ON a.question_id=q.question_id WHERE a.answer_id=$aid LIMIT 1");
    $a = mysqli_fetch_assoc($res);
    if (!$a) { $_SESSION['error']="Jawaban tidak ditemukan."; header('Location: index.php?page=dashboard'); exit; }

    if ($a['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] != 'teacher') {
        $_SESSION['error']="Anda tidak berhak mengedit jawaban ini."; header("Location: index.php?page=view_question&id={$a['question_id']}"); exit;
    }

    require 'view/answer_edit.php';
}

function do_answer_edit(){
    global $conn;
    ensure_login();

    $aid = isset($_POST['answer_id']) ? (int)$_POST['answer_id'] : 0;
    $body = isset($_POST['body']) ? trim($_POST['body']) : '';

    $res = mysqli_query($conn, "SELECT * FROM answer WHERE answer_id=$aid LIMIT 1");
    $a = mysqli_fetch_assoc($res);
    if (!$a) { $_SESSION['error']="Jawaban tidak ditemukan."; header('Location: index.php?page=dashboard'); exit; }

    if ($a['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] != 'teacher') {
        $_SESSION['error']="Anda tidak berhak mengedit jawaban ini."; header("Location: index.php?page=view_question&id={$a['question_id']}"); exit;
    }

    answer_edit($conn, $aid, $_SESSION['user_id'], $body);
    mysqli_query($conn, "INSERT INTO activity_log (user_id, action, detail, created_at) VALUES (".$_SESSION['user_id'].", 'answer_edit', 'answer:$aid', NOW())");

    header("Location: index.php?page=view_question&id=".$a['question_id']);
}

// -------------------------------
// DELETE ANSWER
// -------------------------------
function do_answer_delete(){
    global $conn;
    ensure_login();

    $aid = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $res = mysqli_query($conn, "SELECT * FROM answer WHERE answer_id=$aid LIMIT 1");
    $a = mysqli_fetch_assoc($res);
    if (!$a) { $_SESSION['error']="Jawaban tidak ditemukan."; header('Location: index.php?page=dashboard'); exit; }

    if ($a['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] != 'teacher') {
        $_SESSION['error']="Anda tidak berhak menghapus jawaban ini."; header("Location: index.php?page=view_question&id={$a['question_id']}"); exit;
    }

    answer_delete($conn, $aid);
    mysqli_query($conn, "INSERT INTO activity_log (user_id, action, detail, created_at) VALUES (".$_SESSION['user_id'].", 'answer_delete', 'answer:$aid', NOW())");

    header("Location: index.php?page=view_question&id=".$a['question_id']);
}

// -------------------------------
// EDIT / DELETE COMMENT
// -------------------------------
function do_comment_edit(){
    global $conn;
    ensure_login();

    $cid = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
    $body = isset($_POST['body']) ? trim($_POST['body']) : '';

    $res = mysqli_query($conn, "SELECT * FROM comment WHERE id=$cid LIMIT 1");
    $c = mysqli_fetch_assoc($res);
    if (!$c) { $_SESSION['error']="Komentar tidak ditemukan."; header('Location: index.php?page=dashboard'); exit; }

    if ($c['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] != 'teacher') {
        $_SESSION['error']="Anda tidak berhak mengedit komentar ini."; header("Location: index.php?page=view_question&id=".$c['question_id']); exit;
    }

    comment_edit($conn, $cid, $_SESSION['user_id'], $body);
    mysqli_query($conn, "INSERT INTO activity_log (user_id, action, detail, created_at) VALUES (".$_SESSION['user_id'].", 'comment_edit', 'comment:$cid', NOW())");

    header("Location: index.php?page=view_question&id=".$c['question_id']);
}

function do_comment_delete(){
    global $conn;
    ensure_login();

    $cid = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $res = mysqli_query($conn, "SELECT * FROM comment WHERE id=$cid LIMIT 1");
    $c = mysqli_fetch_assoc($res);
    if (!$c) { $_SESSION['error']="Komentar tidak ditemukan."; header('Location: index.php?page=dashboard'); exit; }

    if ($c['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] != 'teacher') {
        $_SESSION['error']="Anda tidak berhak menghapus komentar ini."; header("Location: index.php?page=view_question&id=".$c['question_id']); exit;
    }

    comment_delete($conn, $cid);
    mysqli_query($conn, "INSERT INTO activity_log (user_id, action, detail, created_at) VALUES (".$_SESSION['user_id'].", 'comment_delete', 'comment:$cid', NOW())");

    header("Location: index.php?page=view_question&id=".$c['question_id']);
}

// -------------------------------
// FILTER BY TAG (redirect helper)
// -------------------------------
function show_questions_by_tag(){
    $tag_id = isset($_GET['tag']) ? (int)$_GET['tag'] : 0;
    if ($tag_id <= 0) { header('Location: index.php?page=dashboard'); exit; }
    // just call dashboard which understands tag param
    header('Location: index.php?page=dashboard&tag='.$tag_id);
}

// -------------------------------
// ADMIN: Manage users (simple wrapper, moved to auth_controller/admin in practice)
// -------------------------------
function admin_users(){
    global $conn;
    ensure_login();
    if ($_SESSION['role'] != 'teacher') { header('Location: index.php?page=dashboard'); exit; }
    $users = user_list_all($conn);
    require 'view/profile.php';
}


?>
