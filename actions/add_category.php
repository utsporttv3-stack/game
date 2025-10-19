<?php
// actions/add_category.php
require_once __DIR__ . '/../config.php';

if (!is_admin()) {
    header('Location: /admin');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['category_name'])) {
    $data = get_data();
    $new_cat_name = htmlspecialchars($_POST['category_name']);
    $new_cat = [
        'id' => time(), 
        'name' => $new_cat_name,
        'slug' => create_slug($new_cat_name)
    ];
    $data['categories'][] = $new_cat;
    save_data($data);
    header('Location: /admin?status=cat_added');
    exit;
}

header('Location: /admin');
exit;
?>

