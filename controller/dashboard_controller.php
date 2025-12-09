<?php

// Load main controller for helper functions
require_once __DIR__ . '/main_controller.php';

if (!isset($_SESSION['user_id'])) {
    redirect('index.php?controller=auth&action=login');
    return;
}

require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../model/question_model.php';
require_once __DIR__ . '/../model/tag_model.php';
require_once __DIR__ . '/../model/user_model.php';


function index_action() {
    // Pagination setup
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $tag_filter = $_GET['tag_id'] ?? null;
    $search_title = $_GET['search_title'] ?? null;

    // Initialize variables
    $questions = null;
    $total_questions = 0;

    if ($search_title) {
        $questions = search_questions($search_title, $limit, $offset);
        // Get total count for pagination
        $search_result = search_questions($search_title);
        $total_questions = $search_result ? mysqli_num_rows($search_result) : 0;
    } elseif ($tag_filter) {
        $questions = fetch_questions_by_tag($tag_filter, $limit, $offset);
        // Get total count for pagination
        $tag_result = fetch_questions_by_tag($tag_filter);
        $total_questions = $tag_result ? mysqli_num_rows($tag_result) : 0;
    } else {
        $questions = get_all_questions($limit, $offset);
        // Get total count for pagination
        global $conn;
        $count_query = "SELECT COUNT(*) as total FROM question q JOIN user u ON q.user_id = u.user_id";
        $count_result = mysqli_query($conn, $count_query);
        $count_row = mysqli_fetch_assoc($count_result);
        $total_questions = $count_row['total'];
    }

    // Calculate total pages
    $total_pages = $total_questions > 0 ? ceil($total_questions / $limit) : 1;

    $tags = get_all_tags();
    if (!$tags) {
        $tags = []; // Set empty array if query fails
    }

    $user = get_user_by_id($_SESSION['user_id']);
    if (!$user) {
        $user = ['name' => 'Unknown User', 'email' => 'Unknown Email', 'school_name' => 'Unknown School'];
    }
    
    $user_questions = get_user_questions($_SESSION['user_id']);
    if (!$user_questions) {
        $user_questions = [];
    }

    // Pass variables to view
    load_view('dashboard', [
        'questions' => $questions,
        'tags' => $tags,
        'user' => $user,
        'user_questions' => $user_questions,
        'page' => $page,
        'total_pages' => $total_pages,
        'tag_filter' => $tag_filter,
        'search_title' => $search_title
    ]);
}
?>

