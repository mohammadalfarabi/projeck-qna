<?php

require_once __DIR__ . '/../koneksi.php';

function get_vote_count($answer_id) {
    global $conn;
    $answer_id = mysqli_real_escape_string($conn, $answer_id);
    $query = "SELECT SUM(vote_value) AS total_votes FROM vote WHERE answer_id = '$answer_id'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total_votes'] ?: 0;
}

function get_user_vote($user_id, $answer_id) {
    global $conn;
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $answer_id = mysqli_real_escape_string($conn, $answer_id);
    $query = "SELECT vote_value FROM vote WHERE user_id = '$user_id' AND answer_id = '$answer_id'";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

function add_vote($user_id, $answer_id, $vote_value) {
    global $conn;
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $answer_id = mysqli_real_escape_string($conn, $answer_id);
    $vote_value = mysqli_real_escape_string($conn, $vote_value);

    // Cek vote
    $existing_vote = get_user_vote($user_id, $answer_id);

    if ($existing_vote) {
        // Update vote
        $query = "UPDATE vote SET vote_value = '$vote_value' 
                  WHERE user_id = '$user_id' AND answer_id = '$answer_id'";
    } else {
        // Insert vote baru (fix NOW → CURRENT_TIMESTAMP)
        $query = "INSERT INTO vote (user_id, answer_id, vote_value, created_at)
                  VALUES ('$user_id', '$answer_id', '$vote_value', CURRENT_TIMESTAMP)";
    }

    return mysqli_query($conn, $query);
}
?>
