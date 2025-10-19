<?php
// actions/login.php
require_once __DIR__ . '/../config.php';

if (isset($_POST['password']) && !empty($_POST['password'])) {
    $data = get_data();
    if (password_verify($_POST['password'], $data['admin_password'])) {
        $_SESSION['is_admin'] = true;
        header('Location: /admin');
        exit;
    }
}

header('Location: /admin?error=1');
exit;
?>

