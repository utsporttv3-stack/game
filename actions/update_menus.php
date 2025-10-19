<?php
// actions/update_menus.php
require_once __DIR__ . '/../config.php';

if (!is_admin()) {
    header('Location: /admin');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = get_data();
    
    $header_links_data = $_POST['header_links'] ?? [];
    $footer_links_data = $_POST['footer_links'] ?? [];
    
    $static_pages = [
        'home' => 'Home', 'about' => 'About Us', 'contact' => 'Contact Us', 
        'privacy' => 'Privacy Policy', 'terms' => 'Terms of Service', 'disclaimer' => 'Disclaimer'
    ];
    $all_categories = array_column($data['categories'], null, 'id');

    $new_header_links = [];
    foreach ($header_links_data as $link_str) {
        list($type, $key) = explode(':', $link_str);
        if ($type === 'page' && isset($static_pages[$key])) {
            $new_header_links[] = ['type' => 'page', 'slug' => $key, 'name' => $static_pages[$key]];
        } elseif ($type === 'category' && isset($all_categories[$key])) {
            $cat = $all_categories[$key];
            $new_header_links[] = ['type' => 'category', 'id' => (int)$key, 'name' => $cat['name'], 'slug' => $cat['slug']];
        }
    }

    $new_footer_links = [];
     foreach ($footer_links_data as $link_str) {
        list($type, $key) = explode(':', $link_str);
        if ($type === 'page' && isset($static_pages[$key])) {
            $new_footer_links[] = ['type' => 'page', 'slug' => $key, 'name' => $static_pages[$key]];
        } elseif ($type === 'category' && isset($all_categories[$key])) {
            $cat = $all_categories[$key];
            $new_footer_links[] = ['type' => 'category', 'id' => (int)$key, 'name' => $cat['name'], 'slug' => $cat['slug']];
        }
    }

    $data['settings']['header_links'] = $new_header_links;
    $data['settings']['footer_links'] = $new_footer_links;
    save_data($data);
    
    header('Location: /admin?status=menus_updated');
    exit;
}

header('Location: /admin');
exit;
?>

