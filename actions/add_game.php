<?php
// actions/add_game.php
require_once __DIR__ . '/../config.php';

if (!is_admin()) {
    header('Location: /admin');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = get_data();
    $new_game = [
        'id' => time(),
        'title' => htmlspecialchars($_POST['title']),
        'slug' => create_slug($_POST['title']),
        'description' => htmlspecialchars($_POST['description']),
        'url' => filter_var($_POST['url'], FILTER_SANITIZE_URL),
        'thumbnail' => filter_var($_POST['thumbnail'], FILTER_SANITIZE_URL),
        'category_id' => (int)$_POST['category_id']
    ];
    $data['games'][] = $new_game;
    save_data($data);
    header('Location: /admin?status=game_added');
    exit;
}

header('Location: /admin');
exit;
?>

