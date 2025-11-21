<?php
// model/vote_model.php

// Vote for answers (vote_value = 1 or -1)
function vote_toggle_answer($conn, $user_id, $answer_id, $value){
    $user_id = (int)$user_id; 
    $answer_id = (int)$answer_id; 
    $value = (int)$value;

    $r = mysqli_query($conn, "
        SELECT * FROM vote 
        WHERE answer_id=$answer_id AND user_id=$user_id 
        LIMIT 1
    ");

    if (mysqli_num_rows($r)) {
        mysqli_query($conn, "
            UPDATE vote 
            SET vote_value=$value, created_at='".date('Y-m-d H:i:s')."' 
            WHERE answer_id=$answer_id AND user_id=$user_id
        ");
    } else {
        mysqli_query($conn, "
            INSERT INTO vote (answer_id, user_id, vote_value, created_at) 
            VALUES ($answer_id, $user_id, $value, '".date('Y-m-d H:i:s')."')
        ");
    }

    // adjust points for answer owner
    $owner = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT user_id FROM answer WHERE answer_id=$answer_id
    "));

    if ($owner) {
        $owner_id = (int)$owner['user_id'];

        // If upvote → +5 points; downvote → -2
        if ($value > 0) user_add_points($conn, $owner_id, 5);
        else user_add_points($conn, $owner_id, -2);
    }

    return true;
}

?>
