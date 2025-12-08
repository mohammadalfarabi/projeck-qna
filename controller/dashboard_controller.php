
<?php

// Helper functions (since they are defined in index.php but not available here)
if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: $url");
        exit;
    }
}

if (!function_exists('load_view')) {
    function load_view($view, $data = []) {
        extract($data);
        include "view/$view.php";
    }
}

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

    if ($search_title) {
        $questions = search_questions($search_title, $limit, $offset);
        // Get total count for pagination
        $total_result = search_questions($search_title);
        $total_questions = mysqli_num_rows($total_result);
    } elseif ($tag_filter) {
        $questions = fetch_questions_by_tag($tag_filter, $limit, $offset);
        // Get total count for pagination
        $total_result = fetch_questions_by_tag($tag_filter);
        $total_questions = mysqli_num_rows($total_result);
    } else {
        $questions = get_all_questions($limit, $offset);
        // Get total count for pagination
        $total_result = get_all_questions();
        $total_questions = mysqli_num_rows($total_result);
    }

    // Calculate total pages
    $total_pages = ceil($total_questions / $limit);

    $tags = get_all_tags();

    $user = get_user_by_id($_SESSION['user_id']);
    $user_questions = get_user_questions($_SESSION['user_id']);

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
