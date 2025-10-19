<?php
// sidebar.php
// This component displays a mobile-friendly, collapsible category list with colorful icons.

// Predefined set of icons (as SVG) and colors (as Tailwind CSS classes)
$icons = [
    // Sword Icon (Action, Adventure)
    '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M13.483.517l2.5 2.5c.5.5.5 1.3 0 1.8l-10 10c-.5.5-1.3.5-1.8 0l-2.5-2.5c-.5-.5-.5-1.3 0-1.8l10-10c.5-.5 1.3-.5 1.8 0zM12.97 1.03a.5.5 0 0 0-.7 0l-10 10a.5.5 0 0 0 0 .7l2.5 2.5a.5.5 0 0 0 .7 0l10-10a.5.5 0 0 0 0-.7l-2.5-2.5z"/><path d="M3.5 13.5a.5.5 0 0 0-.5.5v2a.5.5 0 0 0 .5.5h2a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 0-.5-.5h-2z"/></svg>',
    // Puzzle Icon
    '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M12.5 10V8.5h-2v-2H9V5H7.5V3.5h-2v-2H4V0H2.5v1.5h-2V3H2v1.5H3.5v2H5V8.5h2v2H8.5V12h2v1.5h1.5V15h1.5v-1.5h-2V12h-1.5z"/></svg>',
    // Racing Flag Icon
    '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 1a.5.5 0 0 1 .5.5v13a.5.5 0 0 1-1 0v-13A.5.5 0 0 1 .5 1zM2 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-6a.5.5 0 0 1-.5-.5v-1zm1 1.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-4a.5.5 0 0 1-.5-.5v-1zM2 6.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-5a.5.5 0 0 1-.5-.5v-1zm1 1.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1zM2 10.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-6a.5.5 0 0 1-.5-.5v-1z"/></svg>',
    // Brain Icon (Strategy)
    '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M6.5 2A4.5 4.5 0 0 0 2 6.5v.707a4.5 4.5 0 0 0 1.522 3.21l.398.398a.5.5 0 0 0 .707 0l.398-.398A4.5 4.5 0 0 0 6.5 11.207V14.5a.5.5 0 0 0 1 0v-3.293a4.5 4.5 0 0 0 1.478-1.782l.398-.398a.5.5 0 0 0 .707 0l.398.398A4.5 4.5 0 0 0 14 7.207V6.5A4.5 4.5 0 0 0 9.5 2h-3zM3 6.5A3.5 3.5 0 0 1 6.5 3h3A3.5 3.5 0 0 1 13 6.5v.707a3.5 3.5 0 0 1-1.146 2.475l-.398.398a1.5 1.5 0 0 1-2.122 0l-.398-.398A3.5 3.5 0 0 1 7.5 10.707V13.5a.5.5 0 0 1-1 0v-2.793a3.5 3.5 0 0 1-.954-1.325l-.398-.398a1.5 1.5 0 0 1-2.122 0l-.398.398A3.5 3.5 0 0 1 3 7.207V6.5z"/></svg>',
    // Star Icon (Hypercasual, Fun)
    '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 0L9.8 5.2H16l-4.2 3.4 1.6 5.4-4.4-3.2-4.4 3.2 1.6-5.4L0 5.2h6.2L8 0z"/></svg>',
    // Joystick Icon (Arcade)
    '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1.5a2.5 2.5 0 0 1 2.5 2.5V7a2.5 2.5 0 0 1-5 0V4A2.5 2.5 0 0 1 8 1.5zM4 11.5a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h1zm8 0a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h1zM8 8a1 1 0 0 1 1 1v4a1 1 0 0 1-2 0V9a1 1 0 0 1 1-1z"/></svg>'
];
$colors = [
    'bg-red-500', 'bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-yellow-400', 'bg-pink-500', 'bg-indigo-500', 'bg-teal-500'
];
$icon_count = count($icons);
$color_count = count($colors);
$category_index = 0;
?>
<aside class="w-full md:w-1/4 lg:w-1/5 md:sticky top-24 self-start mb-8 md:mb-0">
    <div class="bg-gray-800 p-4 rounded-lg shadow-lg">
        <!-- **NEW**: Mobile Toggle Button -->
        <button id="category-toggle" class="w-full flex justify-between items-center md:hidden p-2">
            <h3 class="text-xl font-orbitron font-bold text-white">Categories</h3>
            <svg id="category-chevron" class="w-5 h-5 text-white transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
        </button>

        <!-- Desktop Title -->
        <h3 class="text-xl font-orbitron font-bold mb-4 text-white hidden md:block">Categories</h3>
        
        <?php if (!empty($data['categories'])): ?>
        <!-- **NEW**: ID for toggling and classes to hide on mobile by default -->
        <ul id="category-list" class="space-y-2 mt-4 hidden md:block">
            <?php foreach ($data['categories'] as $cat): 
                $game_count = $category_game_counts[$cat['id']] ?? 0;
                $icon = $icons[$category_index % $icon_count];
                $color = $colors[$category_index % $color_count];
                $category_index++;
            ?>
            <li>
                <a href="/category/<?= $cat['slug'] ?>" class="flex items-center text-gray-300 hover:text-white hover:bg-gray-700 p-2 rounded transition-colors duration-200">
                    <span class="w-6 h-6 rounded-md mr-3 flex-shrink-0 flex items-center justify-center text-white <?= $color ?>">
                        <?= $icon ?>
                    </span>
                    <span class="truncate pr-2 flex-grow"><?= htmlspecialchars($cat['name']) ?></span>
                    <span class="bg-gray-900 text-xs font-semibold text-gray-400 px-2 py-1 rounded-full flex-shrink-0"><?= $game_count ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <p class="text-gray-400">No categories found.</p>
        <?php endif; ?>
    </div>
</aside>

<!-- **NEW**: JavaScript to handle the mobile toggle functionality -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('category-toggle');
        const categoryList = document.getElementById('category-list');
        const chevron = document.getElementById('category-chevron');

        if (toggleBtn && categoryList && chevron) {
            toggleBtn.addEventListener('click', () => {
                // Toggle the 'hidden' class to show/hide the list
                categoryList.classList.toggle('hidden');
                // Rotate the chevron icon for visual feedback
                chevron.classList.toggle('rotate-180');
            });
        }
    });
</script>

