<?php
// sidebar.php

// This is the final, corrected template for the category sidebar.
// It is fully responsive and now uses a more reliable method to display game counts.
?>

<!-- Sidebar Column -->
<div class="w-full md:w-1/4 lg:w-1/5">
    <!-- 
      This is the mobile "Categories" button.
      - 'md:hidden' makes it visible only on mobile (screens smaller than medium).
      - Alpine.js (@click) is used to toggle the visibility of the category list.
    -->
    <div x-data="{ open: false }" class="md:hidden mb-4">
        <button @click="open = !open" class="w-full flex items-center justify-between bg-gray-800 text-white font-bold py-3 px-4 rounded-lg">
            <span>Categories</span>
            <!-- Arrow icon that rotates based on the 'open' state -->
            <svg class="w-5 h-5 transition-transform duration-300" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <!-- The category list for mobile, which is shown/hidden by the button. -->
        <div x-show="open" x-transition class="mt-2 bg-gray-800 rounded-lg p-4 space-y-2">
            <?php
            // Loop through each category to display it in the mobile menu.
            foreach ($data['categories'] as $category):
                // *** THE FIX (for mobile) ***
                // We now get the count directly from the pre-calculated global variable.
                // The '?? 0' ensures that if a category has no games, it correctly displays 0.
                $count = $category_game_counts[$category['id']] ?? 0;
            ?>
                 <a href="/category/<?= $category['slug'] ?>" class="flex items-center justify-between text-gray-300 hover:text-white hover:bg-gray-700 p-2 rounded-md transition-colors duration-200">
                    <span class="flex items-center">
                         <span class="w-6 h-6 mr-3 rounded-md flex items-center justify-center bg-gray-700 text-sm">
                            <?= $count > 0 ? '✓' : '' ?>
                        </span>
                        <?= htmlspecialchars($category['name']) ?>
                    </span>
                    <span class="text-sm font-medium text-gray-400 bg-gray-900 px-2 py-1 rounded-full"><?= $count ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 
      This is the desktop sidebar.
      - 'hidden md:block' makes it hidden on mobile and visible on medium screens and up.
      - It's made "sticky" so it stays in place when the user scrolls.
    -->
    <div class="hidden md:block bg-gray-800 rounded-lg p-4 sticky top-24">
        <h3 class="text-xl font-bold font-orbitron mb-4 text-white">Categories</h3>
        <ul class="space-y-2">
            <?php
            // Pre-defined icons and colors to cycle through for a colorful look.
            $colors = ['text-red-400', 'text-blue-400', 'text-green-400', 'text-yellow-400', 'text-indigo-400', 'text-pink-400', 'text-purple-400', 'text-teal-400'];
            $icon_index = 0;

            foreach ($data['categories'] as $category):
                // *** THE FIX (for desktop) ***
                // We get the count from the same pre-calculated global variable.
                $count = $category_game_counts[$category['id']] ?? 0;
                
                // Logic to cycle through the colors.
                $icon_color = $colors[$icon_index % count($colors)];
                $icon_index++;
            ?>
            <li>
                <a href="/category/<?= $category['slug'] ?>" class="flex items-center justify-between text-gray-300 hover:text-white hover:bg-gray-700 p-2 rounded-md transition-colors duration-200">
                    <span class="flex items-center">
                        <span class="w-6 h-6 mr-3 rounded-md flex items-center justify-center <?= $icon_color ?> bg-opacity-20">
                           <!-- Simple Dot Icon -->
                           <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 16 16">
                             <circle cx="8" cy="8" r="8"/>
                           </svg>
                        </span>
                        <?= htmlspecialchars($category['name']) ?>
                    </span>
                    <span class="text-sm font-medium text-gray-400 bg-gray-900 px-2 py-1 rounded-full"><?= $count ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    
    <!-- Alpine.js script for mobile menu interactivity. It's only needed once per page. -->
    <script src="//unpkg.com/alpinejs" defer></script>
</div>

