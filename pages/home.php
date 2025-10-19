<?php
// pages/home.php

// This is the final, fully optimized template for your website's homepage.
// It features a high-performance design that loads instantly and provides a smooth user experience.
?>

<!-- 
  This is the main layout container. It uses Flexbox to create the two-column layout.
  - 'flex-col' on mobile (stacks the sidebar on top of the games)
  - 'md:flex-row' on medium screens and larger (places sidebar next to games)
-->
<div class="flex flex-col md:flex-row gap-8">

    <!-- 
      This line includes the sidebar.php file. 
      Keeping the sidebar in its own file makes the code cleaner and easier to manage.
    -->
    <?php include __DIR__ . '/../sidebar.php'; ?>

    <!-- This is the main content column where the welcome message, search, and games are displayed. -->
    <div class="w-full md:w-3/4 lg:w-4/5">
        
        <!-- Welcome Section -->
        <div class="content-box rounded-lg p-6 mb-8 bg-gray-800 bg-opacity-50">
            <h1 class="text-3xl font-bold mb-4 text-white font-orbitron">Welcome to <?= htmlspecialchars($data['settings']['site_name']) ?>!</h1>
            <p class="text-gray-300">
                <?= htmlspecialchars($data['settings']['site_description']) ?>
            </p>
        </div>

        <!-- Search Bar Section -->
        <div class="mb-8">
            <form action="/search" method="GET">
                <div class="relative">
                    <input type="search" name="q" placeholder="Search for your favorite games..." class="w-full bg-gray-700 text-white rounded-full py-3 pl-12 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                    </div>
                </div>
            </form>
        </div>

        <!-- Games Section Title -->
        <!-- The 'pt-1' class adds a tiny bit of padding to perfectly align this title with the "Categories" title in the sidebar. -->
        <h2 class="text-3xl font-bold font-orbitron mb-6 text-white neon-text pt-1">All Games</h2>
        
        <!-- Game Grid: This is where all the game thumbnails are displayed. -->
        <div id="game-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php 
            $all_games = array_reverse($data['games']); // Show the newest games first
            $initial_games = array_slice($all_games, 0, 15); // PHP only gets the first 15 games
            
            foreach ($initial_games as $game): 
            ?>
                <!-- These first 15 games are rendered directly by PHP for instant loading. -->
                <a href="/game/<?= $game['slug'] ?>" class="block group card-hover-effect game-item">
                    <div class="relative bg-gray-800 rounded-lg overflow-hidden shadow-lg h-full flex flex-col transition-transform duration-300 hover:-translate-y-2">
                        <div class="relative w-full h-40 bg-gray-700">
                            <!-- Image loads instantly, no lazy loading for the first batch. -->
                            <img src="<?= htmlspecialchars($game['thumbnail']) ?>" alt="<?= htmlspecialchars($game['title']) ?>" class="absolute inset-0 w-full h-full object-cover" onerror="this.onerror=null;this.src='https://placehold.co/500x300/1a1a1a/ffffff?text=Image+Not+Found';">
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

        <!-- "More Games" button container -->
        <div class="text-center mt-8">
            <!-- The button is hidden by PHP if there are 15 or fewer games total, ensuring it only appears when needed. -->
            <button id="load-more-btn" class="px-8 py-3 rounded-lg btn-primary text-white font-bold bg-blue-600 hover:bg-blue-700 transition duration-300 ease-in-out transform hover:scale-105" <?= count($all_games) <= 15 ? 'style="display: none;"' : '' ?>>More Games</button>
        </div>

        <!-- JavaScript for the high-performance AJAX "Load More" button -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loadMoreBtn = document.getElementById('load-more-btn');
            const gameGrid = document.getElementById('game-grid');
            let currentOffset = 15; // Start loading games from the 16th position
            let isLoading = false;

            // The IntersectionObserver is for lazy loading images that are loaded via AJAX.
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const image = entry.target;
                        image.src = image.dataset.src;
                        image.classList.remove('lazy');
                        observer.unobserve(image);
                    }
                });
            });

            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', function() {
                    if (isLoading) return; // Prevent multiple clicks while loading
                    isLoading = true;
                    loadMoreBtn.textContent = 'Loading...';

                    // Fetch the next batch of games from the server in the background
                    fetch(`/actions/load_more.php?source=home&offset=${currentOffset}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.html) {
                                // Append the new game cards to the grid
                                const newItems = document.createElement('div');
                                newItems.innerHTML = data.html;
                                Array.from(newItems.children).forEach(item => {
                                    gameGrid.appendChild(item);
                                    // Set up lazy loading for the new images
                                    const lazyImage = item.querySelector('img.lazy');
                                    if (lazyImage) {
                                        imageObserver.observe(lazyImage);
                                    }
                                });
                                
                                currentOffset += 15; // Update the offset for the next click
                            }

                            // If the server says there are no more games, hide the button
                            if (!data.hasMore) {
                                loadMoreBtn.style.display = 'none';
                            }
                        })
                        .catch(error => console.error('Error loading more games:', error))
                        .finally(() => {
                            // Reset the button state after loading is complete
                            isLoading = false;
                            loadMoreBtn.textContent = 'More Games';
                        });
                });
            }
        });
        </script>
    </div>
</div>

