<?php if (!$game): ?>
    <h1 class="text-3xl font-bold">Game not found!</h1>
    <a href="/" class="text-blue-400 hover:underline">Return to Home</a>
<?php else: ?>
    <h1 class="text-4xl font-bold font-orbitron mb-4 text-white neon-text text-center"><?= htmlspecialchars($game['title']) ?></h1>
    <div id="game-container" class="relative w-full bg-black rounded-lg overflow-hidden shadow-2xl shadow-cyan-500/20" style="height: 85vh;">
        <iframe src="<?= htmlspecialchars($game['url']) ?>" frameborder="0" allowfullscreen class="w-full h-full"></iframe>
        <button id="fullscreen-btn" class="absolute bottom-4 right-4 bg-gray-800 bg-opacity-70 text-white p-2 rounded-full hover:bg-gray-700 transition z-10">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>
        </button>
    </div>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="content-box rounded-lg p-6 mt-8">
            <h2 class="text-2xl font-bold mb-4">About This Game</h2>
            <p class="text-gray-300"><?= nl2br(htmlspecialchars($game['description'])) ?></p>
        </div>
        <div class="mt-6">
            <a href="/" class="inline-block px-6 py-3 rounded-lg btn-primary text-white font-bold">&larr; Back to Games</a>
        </div>
    </div>
    <script>
        document.getElementById('fullscreen-btn').addEventListener('click', () => {
            const gameContainer = document.getElementById('game-container');
            if (document.fullscreenElement) { document.exitFullscreen(); } 
            else { gameContainer.requestFullscreen().catch(err => alert(`Error attempting to enable full-screen mode: ${err.message} (${err.name})`)); }
        });
    </script>
<?php endif; ?>
