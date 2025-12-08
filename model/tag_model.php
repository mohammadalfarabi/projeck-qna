<?php

require_once __DIR__ . '/../koneksi.php';

function get_all_tags() {
    global $conn;

    $query = "SELECT `tag_id`, `tag_name` FROM `tag` ORDER BY `tag_id` ASC";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        error_log("DB Error get_all_tags: " . mysqli_error($conn));
        return false;
    }

    return $result;
}

?>
