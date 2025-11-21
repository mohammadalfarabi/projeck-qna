<?php
// model/tag_model.php

function tag_get_or_create($conn, $tag_name){
    $tag = mysqli_real_escape_string($conn, trim($tag_name));
    if (empty($tag)) return false;
    // check existing
    $r = mysqli_query($conn, "SELECT * FROM tags WHERE tag_name='$tag' LIMIT 1");
    if ($row = mysqli_fetch_assoc($r)) return $row['tag_id'];
    // insert
    mysqli_query($conn, "INSERT INTO tags (tag_name) VALUES ('$tag')");
    return mysqli_insert_id($conn);
}

function tag_list_all($conn){
    return mysqli_query($conn, "SELECT * FROM tags ORDER BY tag_name ASC");
}

function tag_get_by_id($conn, $tag_id){
    $tag_id = (int)$tag_id;
    $r = mysqli_query($conn, "SELECT * FROM tags WHERE tag_id=$tag_id LIMIT 1");
    return mysqli_fetch_assoc($r);
}

function tag_get_questions($conn, $tag_id){
    $tag_id = (int)$tag_id;
    $q = "SELECT q.* FROM question q JOIN question_tags qt ON q.question_id = qt.question_id WHERE qt.tag_id=$tag_id ORDER BY q.created_at DESC";
    return mysqli_query($conn, $q);
}
?>
