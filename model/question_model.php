<?php
require_once __DIR__ . '/../koneksi.php';

function get_all_questions($limit = null, $offset = null) {
    global $conn;

    $sql = "
        SELECT q.question_id, q.title, q.body, q.created_at, u.name AS user_name,
               COALESCE(GROUP_CONCAT(t.tag_name ORDER BY t.tag_id ASC SEPARATOR ', '), 'No Tag') AS tags
        FROM `question` q
        LEFT JOIN `user` u ON q.user_id = u.user_id
        LEFT JOIN `question_tag` qt ON q.question_id = qt.question_id
        LEFT JOIN `tag` t ON qt.tag_id = t.tag_id
        GROUP BY q.question_id
        ORDER BY q.question_id DESC
    ";

    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT $limit OFFSET $offset";
    }

    return mysqli_query($conn, $sql);
}


function create_question($user_id, $title, $body, $tag_id) {
    global $conn;

    $user_id = mysqli_real_escape_string($conn, $user_id);
    $title   = mysqli_real_escape_string($conn, $title);
    $body    = mysqli_real_escape_string($conn, $body);

    $query = "INSERT INTO `question` (user_id, title, body, created_at)
              VALUES ('$user_id', '$title', '$body', NOW())";

    mysqli_query($conn, $query);

    $question_id = mysqli_insert_id($conn);

    $queryTag = "INSERT INTO `question_tag` (question_id, tag_id)
                 VALUES ('$question_id', '$tag_id')";

    return mysqli_query($conn, $queryTag);
}


function get_question_by_id($question_id) {
    global $conn;
    $question_id = mysqli_real_escape_string($conn, $question_id);

    $query = "SELECT q.*, u.name AS user_name, s.school_name 
              FROM `question` q
              JOIN `user` u ON q.user_id = u.user_id
              LEFT JOIN `school` s ON u.school_id = s.school_id
              WHERE q.question_id = '$question_id'";

    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}


function get_user_questions($user_id) {
    global $conn;

    $user_id = mysqli_real_escape_string($conn, $user_id);

    $query = "SELECT *
              FROM `question`
              WHERE user_id = '$user_id'
              ORDER BY created_at DESC
              LIMIT 10";

    $result = mysqli_query($conn, $query);

    $questions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $questions[] = $row;
    }
    return $questions;
}


function search_questions($keyword, $limit = null, $offset = null) {
    global $conn;

    $keyword = mysqli_real_escape_string($conn, $keyword);

    $query = "SELECT q.*, u.name AS user_name, s.school_name
              FROM `question` q
              JOIN `user` u ON q.user_id = u.user_id
              LEFT JOIN `school` s ON u.school_id = s.school_id
              WHERE q.title LIKE '%$keyword%'
                 OR q.body LIKE '%$keyword%'
              ORDER BY q.created_at DESC";

    if ($limit !== null && $offset !== null) {
        $query .= " LIMIT $limit OFFSET $offset";
    }

    return mysqli_query($conn, $query);
}


function fetch_questions_by_tag($tag_id = null, $limit = null, $offset = null) {
    global $conn;

    if ($tag_id) {
        $query = "
            SELECT q.question_id, q.title, q.body, q.created_at, u.name AS user_name,
                   GROUP_CONCAT(t.tag_name SEPARATOR ', ') AS tags
            FROM `question` q
            JOIN `user` u ON q.user_id = u.user_id
            JOIN `question_tag` qt ON q.question_id = qt.question_id
            JOIN `tag` t ON qt.tag_id = t.tag_id
            WHERE t.tag_id = '$tag_id'
            GROUP BY q.question_id
            ORDER BY q.question_id DESC
        ";
    } else {
        $query = "
            SELECT q.question_id, q.title, q.body, q.created_at, u.name AS user_name,
                   GROUP_CONCAT(t.tag_name SEPARATOR ', ') AS tags
            FROM `question` q
            JOIN `user` u ON q.user_id = u.user_id
            LEFT JOIN `question_tag` qt ON q.question_id = qt.question_id
            LEFT JOIN `tag` t ON qt.tag_id = t.tag_id
            GROUP BY q.question_id
            ORDER BY q.question_id DESC
        ";
    }

    if ($limit !== null && $offset !== null) {
        $query .= " LIMIT $limit OFFSET $offset";
    }

    return mysqli_query($conn, $query);
}


function assign_tag_to_question($question_id, $tag_id) {
    global $conn;
    return mysqli_query(
        $conn,
        "INSERT INTO `question_tag` (question_id, tag_id) VALUES ('$question_id', '$tag_id')"
    );
}


function get_top_questions_by_comments($school_id) {
    global $conn;

    $school_id = mysqli_real_escape_string($conn, $school_id);

    $query = "SELECT q.question_id, q.title, q.body, u.name AS user_name,
                     COUNT(c.comment_id) AS total_comments
              FROM `question` q
              JOIN `user` u ON q.user_id = u.user_id
              LEFT JOIN `comment` c ON q.question_id = c.question_id
              WHERE u.school_id = '$school_id'
              GROUP BY q.question_id
              ORDER BY total_comments DESC
              LIMIT 10";

    $result = mysqli_query($conn, $query);

    $top_questions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $top_questions[] = $row;
    }
    return $top_questions;
}


function get_top_answers_by_votes_by_school($school_id) {
    global $conn;

    $school_id = mysqli_real_escape_string($conn, $school_id);

    $query = "SELECT a.*, u.name AS user_name, q.title AS question_title,
                     SUM(v.vote_value) AS total_votes
              FROM `answer` a
              JOIN `user` u ON a.user_id = u.user_id
              JOIN `question` q ON a.question_id = q.question_id
              LEFT JOIN `vote` v ON a.answer_id = v.answer_id
              WHERE u.school_id = '$school_id'
              GROUP BY a.answer_id
              ORDER BY total_votes DESC
              LIMIT 10";

    $result = mysqli_query($conn, $query);

    $top_answers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $top_answers[] = $row;
    }
    return $top_answers;
}

?>
