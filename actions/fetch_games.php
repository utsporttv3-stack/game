<?php
// actions/fetch_games.php
require_once __DIR__ . '/../config.php';

if (!is_admin()) {
    header('Location: /admin');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feed_url'])) {
    $feed_url = filter_var($_POST['feed_url'], FILTER_SANITIZE_URL);
    $json_content = @file_get_contents($feed_url);

    if ($json_content === false) {
        header('Location: /admin?status=fetch_error');
        exit;
    }

    $feed_games = json_decode($json_content, true);
    if (!is_array($feed_games)) {
        header('Location: /admin?status=fetch_invalid_json');
        exit;
    }
    
    $data = get_data();
    $count = 0;
    $category_map = array_column($data['categories'], 'id', 'name');

    foreach ($feed_games as $game) {
        if (isset($game['title'], $game['url'], $game['thumb'], $game['category'])) {
            $feed_category_name = htmlspecialchars(trim($game['category']));
            $assigned_category_id = null;

            if (isset($category_map[$feed_category_name])) {
                $assigned_category_id = $category_map[$feed_category_name];
            } else {
                $new_cat_id = time() + $count;
                $new_cat = ['id' => $new_cat_id, 'name' => $feed_category_name, 'slug' => create_slug($feed_category_name)];
                $data['categories'][] = $new_cat;
                $category_map[$feed_category_name] = $new_cat_id;
                $assigned_category_id = $new_cat_id;
            }

            $new_game = [
                'id' => time() + $count,
                'title' => htmlspecialchars($game['title']),
                'slug' => create_slug($game['title']),
                'description' => isset($game['description']) ? htmlspecialchars($game['description']) : '',
                'url' => filter_var($game['url'], FILTER_SANITIZE_URL),
                'thumbnail' => filter_var($game['thumb'], FILTER_SANITIZE_URL),
                'category_id' => $assigned_category_id
            ];
            $data['games'][] = $new_game;
            $count++;
        }
    }
    
    if ($count > 0) {
        save_data($data);
        header('Location: /admin?status=fetch_success&count='.$count);
    } else {
        header('Location: /admin?status=fetch_no_games');
    }
    exit;
}

header('Location: /admin');
exit;
?>

