<?php

// =====================================================================
// GET ALL QUESTIONS
// =====================================================================
function question_get_all($conn, $search = null, $limit = 10, $offset = 0)
{
    $where = "";
    if ($search) {
        $search = mysqli_real_escape_string($conn, $search);
        $where = "WHERE q.title LIKE '%$search%' OR q.body LIKE '%$search%'";
    }

    $sql = "
        SELECT 
            q.question_id,
            q.user_id,
            q.title,
            q.body,
            q.created_at,
            q.views,
            u.name AS username
        FROM question q
        LEFT JOIN user u ON q.user_id = u.user_id
        $where
        ORDER BY q.created_at DESC
        LIMIT $limit OFFSET $offset
    ";

    return mysqli_query($conn, $sql);
}



// =====================================================================
// GET QUESTION BY ID
// =====================================================================
function question_get_by_id($conn, $id)
{
    $id = (int)$id;

    $sql = "
        SELECT 
            q.question_id,
            q.user_id,
            q.title,
            q.body,
            q.created_at,
            q.views,
            u.name AS username
        FROM question q
        LEFT JOIN user u ON q.user_id = u.user_id
        WHERE q.question_id = $id
        LIMIT 1
    ";

    return mysqli_query($conn, $sql);
}



// =====================================================================
// CREATE QUESTION (RETURN ID)
// =====================================================================
function question_create($conn, $user_id, $title, $body)
{
    $title = mysqli_real_escape_string($conn, $title);
    $body  = mysqli_real_escape_string($conn, $body);

    $sql = "
        INSERT INTO question (user_id, title, body, created_at, views)
        VALUES ($user_id, '$title', '$body', NOW(), 0)
    ";

    if (mysqli_query($conn, $sql)) {
        return mysqli_insert_id($conn);
    }

    return false;
}



// =====================================================================
// UPDATE QUESTION
// =====================================================================
function question_edit($conn, $id, $title, $body)
{
    $id    = (int)$id;
    $title = mysqli_real_escape_string($conn, $title);
    $body  = mysqli_real_escape_string($conn, $body);

    $sql = "
        UPDATE question 
        SET title = '$title', body = '$body'
        WHERE question_id = $id
    ";

    return mysqli_query($conn, $sql);
}



// =====================================================================
// DELETE QUESTION
// =====================================================================
function question_delete($conn, $id)
{
    $id = (int)$id;

    // hapus likes
    mysqli_query($conn, "DELETE FROM question_likes WHERE question_id = $id");

    // hapus comments
    mysqli_query($conn, "DELETE FROM comment WHERE question_id = $id");

    // hapus tags
    mysqli_query($conn, "DELETE FROM question_tags WHERE question_id = $id");

    // hapus question
    return mysqli_query($conn, "DELETE FROM question WHERE question_id = $id");
}



// =====================================================================
// INCREMENT VIEWS
// =====================================================================
function question_increment_views($conn, $id)
{
    $id = (int)$id;
    return mysqli_query($conn, "
        UPDATE question 
        SET views = views + 1
        WHERE question_id = $id
    ");
}



// =====================================================================
// LIKE COUNT
// =====================================================================
function question_like_count($conn, $id)
{
    $id = (int)$id;

    $sql = mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM question_likes
        WHERE question_id = $id
    ");

    $row = mysqli_fetch_assoc($sql);
    return $row ? $row['total'] : 0;
}



// =====================================================================
// COMMENT COUNT
// =====================================================================
function comment_count_question($conn, $id)
{
    $id = (int)$id;

    $sql = mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM comment
        WHERE question_id = $id
    ");

    $row = mysqli_fetch_assoc($sql);
    return $row ? $row['total'] : 0;
}



// =====================================================================
// GET TAGS PER QUESTION
// =====================================================================
function question_get_tags($conn, $question_id)
{
    $question_id = (int)$question_id;

    return mysqli_query($conn, "
        SELECT t.tag_id, t.name
        FROM question_tags qt
        JOIN tags t ON qt.tag_id = t.tag_id
        WHERE qt.question_id = $question_id
    ");
}



// =====================================================================
// ATTACH TAG
// =====================================================================
function question_attach_tag($conn, $question_id, $tag_id)
{
    $question_id = (int)$question_id;
    $tag_id = (int)$tag_id;

    return mysqli_query($conn, "
        INSERT INTO question_tags (question_id, tag_id)
        VALUES ($question_id, $tag_id)
    ");
}

// =====================================================================
// REMOVE ALL TAGS
// =====================================================================
function question_detach_all_tags($conn, $question_id)
{
    $question_id = (int)$question_id;

    return mysqli_query($conn, "
        DELETE FROM question_tags 
        WHERE question_id = $question_id
    ");
}

?>
