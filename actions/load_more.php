<?php
// actions/load_more.php

// This script is the engine behind the high-performance "Load More" button.
// It is not a page that users see. Instead, JavaScript fetches data from it in the background.

// Set the content type to JSON, as this script will be sending data, not a full HTML page.
header('Content-Type: application/json');

// Include the main configuration file to get access to the database and helper functions.
require_once __DIR__ . '/../config.php';

// --- INPUT PARAMETERS ---
// Get the data sent by the JavaScript fetch request.
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0; // How many games are already loaded
$source = $_GET['source'] ?? 'home'; // Where is the request from? 'home', 'category', or 'search'
$id = $_GET['id'] ?? null; // The category ID (if source is 'category')
$query = isset($_GET['q']) ? trim(htmlspecialchars($_GET['q'])) : ''; // The search term (if source is 'search')
$limit = 15; // How many games to load per request

// --- GAME FETCHING LOGIC ---
// Based on the 'source', fetch the appropriate list of games.

$games_to_load = [];

switch ($source) {
    case 'category':
        if ($id) {
            $category_id = null;
            // Find the numeric category ID from its slug
            foreach ($data['categories'] as $cat) {
                if ($cat['slug'] === $id) {
                    $category_id = $cat['id'];
                    break;
                }
            }
            if ($category_id) {
                $games_to_load = array_filter($data['games'], fn($g) => $g['category_id'] === $category_id);
            }
        }
        break;

    case 'search':
        if (!empty($query)) {
            $games_to_load = array_filter($data['games'], function($game) use ($query) {
                return stristr($game['title'], $query) || stristr($game['description'], $query);
            });
        }
        break;

    case 'home':
    default:
        $games_to_load = $data['games'];
        break;
}

// Sort the games to show the newest ones first (same as on the main pages)
$games_to_load = array_reverse($games_to_load);

// --- PAGINATION ---
// Slice the array to get only the next batch of games based on the offset and limit.
$games_slice = array_slice($games_to_load, $offset, $limit);

// --- HTML GENERATION ---
// Build the HTML for the game cards that will be sent back to the browser.
$html = '';
foreach ($games_slice as $game) {
    $game_thumbnail = htmlspecialchars($game['thumbnail']);
    $game_title = htmlspecialchars($game['title']);
    $game_slug = $game['slug'];
    $game_description = htmlspecialchars($game['description']);

    // This HTML structure must exactly match the game card structure in home.php, category.php, etc.
    $html .= <<<HTML
    <a href="/game/{$game_slug}" class="block group card-hover-effect game-item">
        <div class="relative bg-gray-800 rounded-lg overflow-hidden shadow-lg h-full flex flex-col transition-transform duration-300 hover:-translate-y-2">
            <div class="relative w-full h-40 bg-gray-700">
                <img src="data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="" class="absolute inset-0 w-full h-full object-cover lazy-placeholder">
                <img data-src="{$game_thumbnail}" alt="{$game_title}" class="lazy absolute inset-0 w-full h-full object-cover" onload="this.classList.add('loaded')" onerror="this.onerror=null;this.src='https://placehold.co/500x300/1a1a1a/ffffff?text=Image+Not+Found'; this.classList.add('loaded');">
            </div>
            <div class="p-4 flex-grow">
                <h3 class="text-lg font-bold text-white truncate">{$game_title}</h3>
                <p class="text-sm text-gray-400 mt-1 text-ellipsis overflow-hidden h-10">{$game_description}</p>
            </div>
            <div class="absolute inset-0 bg-black bg-opacity-70 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <span class="text-white text-xl font-bold font-orbitron">PLAY NOW</span>
            </div>
        </div>
    </a>
HTML;
}

// --- FINAL OUTPUT ---
// Send the generated HTML and a flag indicating if there are more games to load.
echo json_encode([
    'html' => $html,
    'hasMore' => (count($games_to_load) > ($offset + $limit)) // Is the total number of games greater than what's been loaded so far?
]);
?>

