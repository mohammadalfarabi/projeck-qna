<?php
// model/notify_model.php

function notify_create($conn, $user_id, $message){
    $user_id = (int)$user_id;
    $msg = mysqli_real_escape_string($conn, $message);
    $now = date('Y-m-d H:i:s');
    return mysqli_query($conn, "INSERT INTO notifications (user_id, message, created_at) VALUES ($user_id, '$msg', '$now')");
}

function notify_list_for_user($conn, $user_id){
    $user_id = (int)$user_id;
    return mysqli_query($conn, "SELECT * FROM notifications WHERE user_id=$user_id ORDER BY created_at DESC");
}

function notify_mark_read($conn, $notify_id){
    $notify_id = (int)$notify_id;
    return mysqli_query($conn, "UPDATE notifications SET is_read=1 WHERE id=$notify_id");
}

function notify_unread_count($conn, $user_id){
    $user_id = (int)$user_id;
    $r = mysqli_query($conn, "SELECT COUNT(*) AS c FROM notifications WHERE user_id=$user_id AND is_read=0");
    $d = mysqli_fetch_assoc($r);
    return (int)$d['c'];
}
?>
