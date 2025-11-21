<?php
// model/profile_model.php

function profile_upload_photo($conn, $user_id, $file_tmp, $orig_name){
    // Save file into uploads/profile_{user_id}_timestamp.ext
    $ext = pathinfo($orig_name, PATHINFO_EXTENSION);
    $filename = "profile_{$user_id}_" . time() . "." . $ext;
    $uploads_dir = __DIR__ . "/../uploads";
    if (!is_dir($uploads_dir)) mkdir($uploads_dir, 0755, true);
    $dest = $uploads_dir . "/" . $filename;
    if (move_uploaded_file($file_tmp, $dest)) {
        user_set_photo($conn, $user_id, $filename);
        return $filename;
    }
    return false;
}

function profile_get_history($conn, $user_id, $limit=50){
    $user_id = (int)$user_id;
    $q = "SELECT * FROM activity_log WHERE user_id=$user_id ORDER BY created_at DESC LIMIT ".(int)$limit;
    return mysqli_query($conn, $q);
}
?>
