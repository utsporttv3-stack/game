<?php
// pages/category.php

// This file displays all games within a specific category.
// It uses the same high-performance features as the homepage, including the sidebar,
// lazy loading, and a "load more" button.

if (!$category) {
    // If the category slug from the URL is invalid, show a user-friendly error message.
    echo '<h1 class="text-3xl font-bold text-white">Category Not Found</h1>';
    echo '<p class="text-gray-400 mt-4">Sorry, the category you are looking for does not exist.</p>';
    echo '<a href="/" class="text-blue-400 hover:underline mt-4 inline-block"> &larr; Back to Home</a>';
    return; // Stop the script to prevent rendering the rest of the page
}

// Filter the global list of games to get only the ones matching the current category's ID.
$games_in_category = array_filter($data['games'], fn($g) => $g['category_id'] === $category['id']);
?>

<!-- Main Content Grid Layout -->
<div class="flex flex-col md:flex-row gap-8">

    <!-- Include the reusable, mobile-friendly sidebar -->
    <?php include __DIR__ . '/../sidebar.php'; ?>

    <!-- Main Content Column -->
    <div class="w-full md:w-3/4 lg:w-4/5">

        <!-- Page Title: Displays the name of the current category. -->
        <!-- The pt-1 class ensures pixel-perfect alignment with the sidebar title. -->
        <h1 class="text-3xl font-bold font-orbitron mb-6 text-white neon-text pt-1">Category: <?= htmlspecialchars($category['name']) ?></h1>

        <?php if (empty($games_in_category)): ?>
            <p class="text-gray-400">There are no games in this category yet.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php
                $all_games_in_category = array_reverse($games_in_category); // Show newest games first
                $game_index = 0;
                foreach ($all_games_in_category as $game):
                    // Hybrid loading logic: first 15 load instantly, the rest lazy load.
                    $is_above_the_fold = $game_index < 15;
                    $is_visible = $game_index < 15;
                    $game_index++;
                ?>
                    <!-- The 'game-item' class is used by JavaScript for the "load more" feature. -->
                    <!-- Games after the 15th are hidden initially using PHP for a faster load. -->
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
                <!-- The "More Games" button is hidden by PHP if there are 15 or fewer games. -->
                <button id="load-more-btn" class="px-8 py-3 rounded-lg btn-primary text-white font-bold bg-blue-600 hover:bg-blue-700 transition duration-300 ease-in-out transform hover:scale-105" <?= count($all_games_in_category) <= 15 ? 'style="display: none;"' : '' ?>>More Games</button>
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

