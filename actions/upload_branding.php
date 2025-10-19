<?php
// actions/upload_branding.php

// This script handles the logo and favicon uploads from the admin panel.

// It's crucial to include the main config file to access helper functions and the database.
require_once __DIR__ . '/../config.php';

// Security check: Only admins can perform this action.
if (!is_admin()) {
    header('Location: /admin'); // Redirect non-admins
    exit;
}

/**
 * Handles a single file upload, validates it, and moves it to the /uploads/ directory.
 * @param string $file_key The key from the $_FILES array (e.g., 'favicon_image').
 * @param array $allowed_types An array of allowed file extensions (e.g., ['png', 'ico']).
 * @return array An array with either a 'success' key (with the new file path) or an 'error' key.
 */
function handle_upload($file_key, $allowed_types) {
    if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES[$file_key];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_types)) {
            return ['error' => "Invalid file type for $file_key. Allowed types: " . implode(', ', $allowed_types)];
        }

        if ($file['size'] > 2 * 1024 * 1024) { // 2MB file size limit
            return ['error' => 'File is too large. Maximum size is 2MB.'];
        }

        $upload_dir = __DIR__ . '/../uploads/';
        if (!is_dir($upload_dir)) {
            // Attempt to create the directory if it doesn't exist
            if (!mkdir($upload_dir, 0755, true)) {
                return ['error' => 'Uploads directory does not exist and could not be created.'];
            }
        }

        // Create a unique filename to prevent overwriting existing files.
        $new_filename = uniqid($file_key . '-', true) . '.' . $file_ext;
        $destination = $upload_dir . $new_filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // On success, return the public URL path to the file.
            return ['success' => '/uploads/' . $new_filename];
        } else {
            return ['error' => 'Failed to move the uploaded file. Check folder permissions.'];
        }
    }
    return ['error' => null]; // Return null if no file was uploaded for this key.
}

// Check if the form was submitted.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = get_data();
    $messages = [];
    
    // Handle the favicon upload.
    $favicon_result = handle_upload('favicon_image', ['png', 'ico', 'svg']);
    if (isset($favicon_result['success'])) {
        $data['settings']['favicon_url'] = $favicon_result['success'];
        $messages[] = 'Favicon uploaded successfully.';
    } elseif (isset($favicon_result['error'])) {
        $messages[] = 'Favicon Error: ' . $favicon_result['error'];
    }

    // Save the updated data to the database file.
    save_data($data);
    
    // Determine if the overall operation was a success or error for the status message.
    $status_type = (isset($favicon_result['error'])) ? 'error' : 'success';
    $message = urlencode(implode(' ', $messages));
    
    // Redirect back to the admin panel with a status message.
    header("Location: /admin?status=branding_updated&type=$status_type&msg=$message");
    exit;
}

// If someone accesses this file directly without submitting the form, redirect them.
header('Location: /admin');
exit;
?>

