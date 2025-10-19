<?php
// actions/update_ads_txt.php

// Look for config.php one directory up from the current file's location.
require_once __DIR__ . '/../config.php';

// Security check: Only admins can perform this action.
if (!is_admin()) {
    header('Location: /admin'); // Redirect non-admins
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the content from the form.
    // We use trim to remove any accidental whitespace from the beginning or end.
    $content = trim($_POST['ads_txt_content'] ?? '');

    // Define the path to ads.txt in the root directory (one level up from /actions/)
    $ads_txt_file = __DIR__ . '/../ads.txt';

    // Try to write the content to the file
    if (@file_put_contents($ads_txt_file, $content) !== false) {
        // Success: Redirect back with a success message
        header('Location: /admin?status=ads_txt_updated');
    } else {
        // Failure: Redirect back with an error message (this is usually a file permissions issue)
        header('Location: /admin?status=ads_txt_error');
    }
    exit;
}

// Redirect if accessed directly via GET request
header('Location: /admin');
exit;
?>
