<?php
// pages/admin.php

// If not admin, show login form and stop rendering the page
if (!is_admin()) {
    $login_error = $_GET['error'] ?? null;
?>
    <div class="max-w-md mx-auto mt-10 bg-gray-800 p-8 rounded-lg shadow-2xl shadow-cyan-500/20">
        <h2 class="text-2xl font-bold font-orbitron text-center mb-6 neon-text">Admin Login</h2>
        <?php if ($login_error): ?>
            <p class="bg-red-500 text-white p-3 rounded-md mb-4">Invalid password!</p>
        <?php endif; ?>
        <form action="/actions/login.php" method="POST">
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                <input type="password" name="password" id="password" class="w-full px-3 py-2 rounded-md bg-gray-700 border border-gray-600 focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <button type="submit" class="w-full py-3 rounded-lg btn-primary text-white font-bold bg-blue-600 hover:bg-blue-700">Login</button>
        </form>
    </div>
<?php return; } // End the script here if not logged in

// --- Admin Dashboard Content ---
$status = $_GET['status'] ?? '';
$status_messages = [
    'game_added' => ['type' => 'success', 'text' => 'Game added successfully!'],
    'game_deleted' => ['type' => 'success', 'text' => 'Game deleted successfully!'],
    'cat_added' => ['type' => 'success', 'text' => 'Category added successfully!'],
    'cat_deleted' => ['type' => 'success', 'text' => 'Category deleted successfully!'],
    'fetch_success' => ['type' => 'success', 'text' => 'Fetch successful! Added ' . ($_GET['count'] ?? 0) . ' games.'],
    'menus_updated' => ['type' => 'success', 'text' => 'Header and footer menus updated successfully!'],
    'ads_updated' => ['type' => 'success', 'text' => 'ads.txt file updated successfully!'],
    'fetch_error' => ['type' => 'error', 'text' => 'Could not fetch feed. Check the URL and try again.'],
    'fetch_invalid_json' => ['type' => 'error', 'text' => 'The data from the URL was not valid JSON.'],
    'fetch_no_games' => ['type' => 'error', 'text' => 'No valid games were found in the feed. Check the feed format.'],
    'branding_updated' => ['type' => $_GET['type'] ?? 'success', 'text' => $_GET['msg'] ?? 'Branding updated successfully!'],
];
?>
<div class="admin-panel">
    <h1 class="text-4xl font-bold font-orbitron mb-8 neon-text">Admin Dashboard</h1>
    
    <?php if (isset($status_messages[$status])): 
        $msg = $status_messages[$status]; 
        $color = $msg['type'] === 'success' ? 'green' : 'red'; 
    ?>
        <div class="bg-<?= $color ?>-500 bg-opacity-30 border border-<?= $color ?>-500 text-<?= $color ?>-300 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline"><?= htmlspecialchars(urldecode($msg['text'])) ?></span>
        </div>
    <?php endif; ?>

    <!-- Branding Settings Form -->
    <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
         <h2 class="text-2xl font-orbitron font-bold mb-4">Branding Settings</h2>
         <form action="/actions/upload_branding.php" method="POST" enctype="multipart/form-data">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                     <label for="favicon_image" class="block text-sm font-medium mb-2">Upload Favicon (PNG, ICO, SVG)</label>
                    <input type="file" name="favicon_image" id="favicon_image" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-500 file:text-white hover:file:bg-blue-600">
                    <?php if (!empty($data['settings']['favicon_url'])): ?>
                        <div class="mt-4">
                            <p class="text-sm text-gray-400">Current Favicon:</p>
                            <img src="<?= htmlspecialchars($data['settings']['favicon_url']) ?>" alt="Current Favicon" class="mt-2 h-8 w-8 bg-gray-700 p-1 rounded">
                        </div>
                    <?php endif; ?>
                </div>
                 <!-- This is where a logo upload could go in the future -->
                 <div></div>
            </div>
            <button type="submit" class="mt-6 w-full py-3 rounded-lg btn-primary text-white font-bold bg-blue-600 hover:bg-blue-700">Save Branding</button>
         </form>
    </div>

    <!-- Manage ads.txt -->
    <div class="mt-8 bg-gray-800 p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-orbitron font-bold mb-4">Manage ads.txt</h2>
        <form action="/actions/update_ads_txt.php" method="POST">
            <textarea name="ads_txt_content" rows="8" class="w-full rounded-md bg-gray-700 border-gray-600 text-sm font-mono" placeholder="google.com, pub-0000000000000000, DIRECT, f08c47fec0942fa0"><?= htmlspecialchars(get_ads_txt_content()) ?></textarea>
            <p class="text-xs text-gray-400 mt-2">Enter the content for your ads.txt file here. This is crucial for ad providers like AdSense.</p>
            <button type="submit" class="mt-4 w-full py-3 rounded-lg btn-primary text-white font-bold bg-blue-600 hover:bg-blue-700">Save ads.txt</button>
        </form>
    </div>

    <!-- Game Management Forms -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-orbitron font-bold mb-4">Add a New Game Manually</h2>
            <form action="/actions/add_game.php" method="POST" class="space-y-4">
                <div><label for="title" class="block text-sm font-medium">Title</label><input type="text" name="title" id="title" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600" required></div>
                 <div><label for="description" class="block text-sm font-medium">Description</label><p class="text-xs text-gray-400">Important: Write a unique, high-quality description (150+ words) to help with SEO and AdSense approval.</p><textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600"></textarea></div>
                <div><label for="url" class="block text-sm font-medium">Game URL (embed link)</label><input type="url" name="url" id="url" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600" required></div>
                <div><label for="thumbnail" class="block text-sm font-medium">Thumbnail URL</label><input type="url" name="thumbnail" id="thumbnail" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600" required></div>
                <div><label for="category_id" class="block text-sm font-medium">Category</label><select name="category_id" id="category_id" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600" required><?php foreach ($data['categories'] as $cat): ?><option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option><?php endforeach; ?></select></div>
                <button type="submit" class="w-full py-3 rounded-lg btn-primary text-white font-bold bg-blue-600 hover:bg-blue-700">Add Game</button>
            </form>
        </div>
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-orbitron font-bold mb-4">Fetch Games from Feed URL</h2>
             <form action="/actions/fetch_games.php" method="POST" class="space-y-4">
                <div>
                     <label for="feed_url" class="block text-sm font-medium">Game Feed URL (JSON format)</label>
                     <p class="text-xs text-gray-400 mb-2">e.g., from GameMonetize. Categories will be created automatically.</p>
                     <input type="url" name="feed_url" id="feed_url" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600" placeholder="https://rss.gamemonetize.com/rss.php?..." required>
                </div>
                 <div class="pt-2">
                    <button type="submit" class="w-full py-3 rounded-lg btn-primary text-white font-bold bg-blue-600 hover:bg-blue-700">Fetch & Import Games</button>
                 </div>
            </form>
        </div>
    </div>
    
    <!-- Site & Menu Settings -->
    <div class="mt-10 bg-gray-800 p-6 rounded-lg shadow-lg">
         <h2 class="text-2xl font-orbitron font-bold mb-4">Site & Menu Settings</h2>
         <form action="/actions/update_menus.php" method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">Header Menu Links</h3>
                    <div class="space-y-2 p-4 rounded-md bg-gray-900 max-h-60 overflow-y-auto">
                        <?php 
                        $static_pages = ['home' => 'Home', 'about' => 'About Us', 'contact' => 'Contact Us', 'privacy' => 'Privacy Policy', 'terms' => 'Terms of Service', 'disclaimer' => 'Disclaimer'];
                        $current_header_links = array_map(fn($l) => $l['type'].':'.($l['slug'] ?? $l['id']), $data['settings']['header_links']);

                        echo '<h4 class="font-bold text-gray-400 text-sm mb-2">Pages</h4>';
                        foreach ($static_pages as $slug => $name) {
                            $id = "h-page-$slug";
                            $value = "page:$slug";
                            $checked = in_array($value, $current_header_links) ? 'checked' : '';
                            echo "<div><input type='checkbox' name='header_links[]' value='$value' id='$id' $checked><label for='$id' class='ml-2'>$name</label></div>";
                        }
                        echo '<h4 class="font-bold text-gray-400 text-sm mt-4 mb-2">Categories</h4>';
                        foreach ($data['categories'] as $cat) {
                            $id = "h-cat-{$cat['id']}";
                            $value = "category:{$cat['id']}";
                            $checked = in_array($value, $current_header_links) ? 'checked' : '';
                            echo "<div><input type='checkbox' name='header_links[]' value='$value' id='$id' $checked><label for='$id' class='ml-2'>".htmlspecialchars($cat['name'])."</label></div>";
                        }
                        ?>
                    </div>
                </div>
                 <div>
                    <h3 class="text-xl font-bold mb-4">Footer Menu Links</h3>
                     <div class="space-y-2 p-4 rounded-md bg-gray-900 max-h-60 overflow-y-auto">
                        <?php 
                        $current_footer_links = array_map(fn($l) => $l['type'].':'.($l['slug'] ?? $l['id']), $data['settings']['footer_links']);

                        echo '<h4 class="font-bold text-gray-400 text-sm mb-2">Pages</h4>';
                        foreach ($static_pages as $slug => $name) {
                            $id = "f-page-$slug";
                            $value = "page:$slug";
                            $checked = in_array($value, $current_footer_links) ? 'checked' : '';
                            echo "<div><input type='checkbox' name='footer_links[]' value='$value' id='$id' $checked><label for='$id' class='ml-2'>$name</label></div>";
                        }
                        echo '<h4 class="font-bold text-gray-400 text-sm mt-4 mb-2">Categories</h4>';
                        foreach ($data['categories'] as $cat) {
                            $id = "f-cat-{$cat['id']}";
                            $value = "category:{$cat['id']}";
                            $checked = in_array($value, $current_footer_links) ? 'checked' : '';
                            echo "<div><input type='checkbox' name='footer_links[]' value='$value' id='$id' $checked><label for='$id' class='ml-2'>".htmlspecialchars($cat['name'])."</label></div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
            <button type="submit" class="mt-6 w-full py-3 rounded-lg btn-primary text-white font-bold bg-blue-600 hover:bg-blue-700">Save Menu Settings</button>
         </form>
    </div>

    <!-- Manage Content -->
    <div class="mt-10 bg-gray-800 p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-orbitron font-bold mb-4">Manage Content</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div id="categories-management"> 
                <h3 class="text-xl font-bold mb-4">Categories</h3> 
                <form action="/actions/add_category.php" method="POST" class="flex gap-2 mb-4">
                    <input type="text" name="category_name" placeholder="New category name" class="flex-grow rounded-md bg-gray-700 border-gray-600" required>
                    <button type="submit" class="px-4 py-2 rounded-lg btn-primary text-white font-bold bg-blue-600 hover:bg-blue-700">Add</button>
                </form>
                <ul class="space-y-2 max-h-72 overflow-y-auto"><?php foreach ($data['categories'] as $cat): ?>
                    <li class="flex justify-between items-center bg-gray-700 p-2 rounded">
                        <span><?= htmlspecialchars($cat['name']) ?></span>
                        <a href="/?delete_category=<?= $cat['id'] ?>" class="text-red-400 hover:text-red-300" onclick="return confirm('Are you sure? This will also delete all games in this category!')">&times; Delete</a>
                    </li>
                <?php endforeach; ?></ul>
            </div>
            <div id="games-management">
                <h3 class="text-xl font-bold mb-4">Recent Games</h3>
                <div class="overflow-y-auto max-h-96">
                    <ul><?php foreach (array_reverse($data['games']) as $game): ?>
                        <li class="flex justify-between items-center bg-gray-700 p-2 rounded mb-2">
                            <span><?= htmlspecialchars($game['title']) ?></span>
                            <a href="/?delete_game=<?= $game['id'] ?>" class="text-red-400 hover:text-red-300" onclick="return confirm('Are you sure?')">Delete</a>
                        </li>
                    <?php endforeach; ?></ul>
                </div>
            </div>
        </div>
    </div>
</div>

