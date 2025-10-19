<?php
// ======================================================================
// G A M E H U B - Main Router
// ======================================================================

// --- ERROR REPORTING (for debugging) ---
// Comment these out on a live production server
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

// --- HANDLE DELETION ACTIONS ---
if (is_admin()) {
    if (isset($_GET['delete_game'])) {
        $game_id = (int)$_GET['delete_game'];
        $data['games'] = array_filter($data['games'], fn($g) => $g['id'] !== $game_id);
        save_data($data);
        header('Location: /admin?status=game_deleted');
        exit;
    }
    if (isset($_GET['delete_category'])) {
        $cat_id = (int)$_GET['delete_category'];
        // Also delete games in this category
        $data['games'] = array_filter($data['games'], fn($g) => $g['category_id'] !== $cat_id);
        $data['categories'] = array_filter($data['categories'], fn($c) => $c['id'] !== $cat_id);
        save_data($data);
        header('Location: /admin?status=cat_deleted');
        exit;
    }
}

// --- ROUTING LOGIC ---
$path = trim($_GET['path'] ?? '', '/');
$path_parts = explode('/', $path);
$action = $path_parts[0] ?? 'home';
$slug = $path_parts[1] ?? null;

if ($action === 'logout') {
    session_destroy();
    header('Location: /');
    exit;
}
if (empty($action)) {
    $action = 'home';
}

// --- PAGE SELECTION & DATA PREPARATION ---
$page_file = 'pages/' . $action . '.php';

$page_title = ucfirst($action);
$meta_description = $data['settings']['site_description'] ?? 'Your ultimate destination for high-quality browser games.';
$is_full_width = false;

switch ($action) {
    case 'game':
        $page_file = 'pages/game.php';
        $game = null;
        foreach($data['games'] as $g) {
            if(isset($g['slug']) && $g['slug'] === $slug) {
                $game = $g;
                break;
            }
        }
        if ($game) {
            $page_title = $game['title'];
            $meta_description = htmlspecialchars(substr($game['description'], 0, 160));
        } else {
            $page_title = 'Game Not Found';
        }
        $is_full_width = true;
        break;

    case 'category':
         $page_file = 'pages/category.php';
         $category = null;
         foreach($data['categories'] as $c) {
             if(isset($c['slug']) && $c['slug'] === $slug) {
                 $category = $c;
                 break;
             }
         }
         if ($category) {
             $page_title = $category['name'];
             $meta_description = "Play the best games in the {$page_title} category.";
         } else {
             $page_title = 'Category Not Found';
         }
         break;

    // **NEW** Search case
    case 'search':
        $page_file = 'pages/search.php';
        $query = $_GET['q'] ?? '';
        $page_title = 'Search results for "' . htmlspecialchars($query) . '"';
        break;

    case 'home':
        $page_file = 'pages/home.php';
        $page_title = 'Home';
        break;
        
    default:
        if (!file_exists($page_file)) {
            http_response_code(404);
            $page_file = 'pages/404.php';
            $page_title = 'Page Not Found';
        }
        break;
}

// --- RENDER THE FINAL PAGE ---
include 'header.php';
include $page_file;
include 'footer.php';
?>

