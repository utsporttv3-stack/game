<?php
// pages/search.php

// This file handles displaying the results for a user's search query.
// It shares the same high-performance layout and features as the homepage and category pages.

// Get the search query from the URL (?q=...) and sanitize it to prevent security issues.
$search_query = isset($_GET['q']) ? trim(htmlspecialchars($_GET['q'])) : '';

$search_results = [];
if (!empty($search_query)) {
    // If there is a search query, filter the main games array.
    // The search is case-insensitive (stristr) and checks both the title and description.
    $search_results = array_filter($data['games'], function($game) use ($search_query) {
        return stristr($game['title'], $search_query) || stristr($game['description'], $search_query);
    });
}
?>

<!-- Main Content Grid Layout -->
<div class="flex flex-col md:flex-row gap-8">

    <!-- Include the reusable, mobile-friendly sidebar -->
    <?php include __DIR__ . '/../sidebar.php'; ?>

    <!-- Main Content Column -->
    <div class="w-full md:w-3/4 lg:w-4/5">

        <!-- Search Bar: Included again here so users can easily refine their search. -->
        <!-- The 'value' attribute is pre-filled with the current search query. -->
        <div class="mb-8">
            <form action="/search" method="GET">
                <div class="relative">
                    <input type="search" name="q" placeholder="Search for your favorite games..." class="w-full bg-gray-700 text-white rounded-full py-3 pl-12 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg" value="<?= $search_query ?>">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Page Title: Displays the search term to the user. -->
        <h1 class="text-3xl font-bold font-orbitron mb-6 text-white neon-text pt-1">
            <?php if (!empty($search_query)): ?>
                Search Results for "<?= $search_query ?>"
            <?php else: ?>
                Please enter a search term
            <?php endif; ?>
        </h1>

        <?php if (empty($search_results)): ?>
            <p class="text-gray-400">No games found matching your search. Please try a different keyword.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php
                $game_index = 0;
                foreach ($search_results as $game):
                    // Hybrid loading logic: first 15 load instantly, the rest lazy load.
                    $is_above_the_fold = $game_index < 15;
                    $is_visible = $game_index < 15;
                    $game_index++;
                ?>
                    <a href="/game/<?= $game['slug'] ?>" class="block group card-hover-effect game-item" <?= !$is_visible ? 'style="display: none;"' : '' ?>>
                        <div class="relative bg-gray-800 rounded-lg overflow-hidden shadow-lg h-full flex flex-col transition-transform duration-300 hover:-translate-y-2">
                            <div class="relative w-full h-40 bg-gray-700">
                                <?php if ($is_above_the_fold): // INSTANT LOADING ?>
                                    <img src="<?= htmlspecialchars($game['thumbnail']) ?>" alt="<?= htmlspecialchars($game['title']) ?>" class="absolute inset-0 w-full h-full object-cover" onerror="this.onerror=null;this.src='https://placehold.co/500x300/1a1a1a/ffffff?text=Image+Not+Found';">
                                <?php else: // LAZY LOADING ?>
                                    <img src="data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="" class="absolute inset-0 w-full h-full object-cover lazy-placeholder">
                                    <img data-src="<?= htmlspecialchars($game['thumbnail']) ?>" alt="<?= htmlspecialchars($game['title']) ?>" class="lazy absolute inset-0 w-full h-full object-cover" onload="this.classList.add('loaded')" onerror="this.onerror=null;this.src='https://placehold.co/500x300/1a1a1a/ffffff?text=Image+Not+Found'; this.classList.add('loaded');">
                                <?php endif; ?>
                            </div>
                            <div class="p-4 flex-grow">
                                <h3 class="text-lg font-bold text-white truncate"><?= htmlspecialchars($game['title']) ?></h3>
                                <p class="text-sm text-gray-400 mt-1 text-ellipsis overflow-hidden h-10"><?= htmlspecialchars($game['description']) ?></p>
                            </div>
                            <div class="absolute inset-0 bg-black bg-opacity-70 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <span class="text-white text-xl font-bold font-orbitron">PLAY NOW</span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-8">
                <!-- "More Games" button is hidden via PHP if there are 15 or fewer results -->
                <button id="load-more-btn" class="px-8 py-3 rounded-lg btn-primary text-white font-bold bg-blue-600 hover:bg-blue-700 transition duration-300 ease-in-out transform hover:scale-105" <?= count($search_results) <= 15 ? 'style="display: none;"' : '' ?>>More Games</button>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Smooth Lazy Loading for Images
                const lazyImages = document.querySelectorAll('img.lazy');
                 if ("IntersectionObserver" in window) {
                    const imageObserver = new IntersectionObserver(function(entries, observer) {
                        entries.forEach(function(entry) {
                            if (entry.isIntersecting) {
                                const image = entry.target;
                                image.src = image.dataset.src;
                                imageObserver.unobserve(image);
                            }
                        });
                    });
                    lazyImages.forEach(image => imageObserver.observe(image));
                }

                // "Load More" Button Functionality
                const gameItems = document.querySelectorAll('.game-item');
                const loadMoreBtn = document.getElementById('load-more-btn');
                const itemsPerClick = 15;
                let visibleItems = 15;

                if (loadMoreBtn) {
                    loadMoreBtn.addEventListener('click', function() {
                        let newVisibleItems = visibleItems + itemsPerClick;
                        for (let i = visibleItems; i < newVisibleItems && i < gameItems.length; i++) {
                            gameItems[i].style.display = 'block';
                        }
                        visibleItems = newVisibleItems;
                        if (visibleItems >= gameItems.length) {
                            loadMoreBtn.style.display = 'none';
                        }
                    });
                }
            });
            </script>
        <?php endif; ?>
    </div>
</div>

