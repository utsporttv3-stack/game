<?php
// config.php

// This is the central configuration and function file for the entire website.
// It handles session management, database connection, and provides helper functions.

// --- ERROR REPORTING (for debugging) ---
// It's helpful to show errors during development.
// On a live production server, you should comment these lines out for security.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start the session to manage the admin's logged-in state.
session_start();

// --- CORE DEFINITIONS ---
// Define the path for our simple JSON database file. Using __DIR__ makes the path reliable.
define('DB_FILE', __DIR__ . '/database.json');
// Define the path for the ads.txt file.
define('ADS_TXT_FILE', __DIR__ . '/ads.txt');
// Default admin password. IMPORTANT: Change this for security on a live site.
define('DEFAULT_ADMIN_PASSWORD', 'admin123');
// Default contact email for the "Contact Us" page.
define('CONTACT_EMAIL', 'contact@yourwebsite.com');


// --- HELPER FUNCTIONS ---

/**
 * Reads data from the JSON database file.
 * If the file doesn't exist, it creates it with a default structure.
 * @return array The website's data as a PHP array.
 */
function get_data() {
    if (!file_exists(DB_FILE)) {
        // This is the default structure for a new website.
        $default_data = [
            'admin_password' => password_hash(DEFAULT_ADMIN_PASSWORD, PASSWORD_DEFAULT),
            'settings' => [
                'site_name' => 'GameHub',
                'site_description' => 'Your ultimate destination for high-quality browser games, reviews, and community fun.',
                'favicon_url' => '', // Initially empty
                'header_links' => [
                    ['type' => 'page', 'slug' => 'home', 'name' => 'Home'],
                    ['type' => 'page', 'slug' => 'about', 'name' => 'About Us'],
                    ['type' => 'page', 'slug' => 'contact', 'name' => 'Contact'],
                ],
                'footer_links' => [
                    ['type' => 'page', 'slug' => 'about', 'name' => 'About Us'],
                    ['type' => 'page', 'slug' => 'contact', 'name' => 'Contact Us'],
                    ['type' => 'page', 'slug' => 'privacy', 'name' => 'Privacy Policy'],
                ]
            ],
            'categories' => [],
            'games' => []
        ];
        // Save the default structure to the new file.
        file_put_contents(DB_FILE, json_encode($default_data, JSON_PRETTY_PRINT));
        return $default_data;
    }
    // If the file exists, read it and decode the JSON into a PHP array.
    return json_decode(file_get_contents(DB_FILE), true);
}

/**
 * Saves a PHP array back to the JSON database file.
 * @param array $data The data to be saved.
 */
function save_data($data) {
    file_put_contents(DB_FILE, json_encode($data, JSON_PRETTY_PRINT));
}

/**
 * Checks if the current user is logged in as an admin.
 * @return bool True if logged in, false otherwise.
 */
function is_admin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

/**
 * Creates a URL-friendly "slug" from a string.
 * Example: "My Awesome Game!" becomes "my-awesome-game".
 * @param string $text The input string.
 * @return string The URL-friendly slug.
 */
function create_slug($text) {
    // Replace non-letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Trim
    $text = trim($text, '-');
    // Remove duplicate -
    $text = preg_replace('~-+~', '-', $text);
    // Lowercase
    $text = strtolower($text);
    if (empty($text)) {
        return 'n-a';
    }
    return $text;
}

/**
 * *** THE FIX (Part 1) ***
 * Counts the number of games in each category efficiently.
 * This function is now the single source of truth for game counts.
 * @param array $games The full list of games.
 * @return array An associative array where keys are category IDs and values are game counts.
 */
function get_category_game_counts($games) {
    $counts = [];
    foreach ($games as $game) {
        if (isset($game['category_id'])) {
            $cat_id = $game['category_id'];
            if (!isset($counts[$cat_id])) {
                $counts[$cat_id] = 0;
            }
            $counts[$cat_id]++;
        }
    }
    return $counts;
}

/**
 * Reads the content of the ads.txt file.
 * If the file doesn't exist, it returns an empty string.
 * @return string The content of ads.txt.
 */
function get_ads_txt_content() {
    if (file_exists(ADS_TXT_FILE)) {
        return file_get_contents(ADS_TXT_FILE);
    }
    return '';
}


// --- INITIAL DATA LOAD ---
// Load all website data from the database into a global variable for easy access.
$data = get_data();

// *** THE FIX (Part 2) ***
// Pre-calculate the game counts and store them in a new global variable.
// This variable will be available everywhere, including in sidebar.php.
$category_game_counts = get_category_game_counts($data['games']);
?>

