<?php
// footer.php
?>
            </div>
        </main>

        <footer class="bg-gray-800 mt-auto">
            <div class="container mx-auto py-6 px-4 sm:px-6 lg:px-8 text-center text-gray-400">
                 <div class="flex justify-center space-x-6 mb-4">
                    <?php 
                    if (isset($data['settings']['footer_links']) && is_array($data['settings']['footer_links'])):
                        foreach ($data['settings']['footer_links'] as $link): 
                            $href = '#';
                            if (isset($link['slug'])) {
                                $href = $link['type'] === 'page' ? '/'.$link['slug'] : '/category/'.$link['slug'];
                                if ($link['slug'] === 'home') $href = '/';
                            }
                        ?>
                            <a href="<?= $href ?>" class="hover:text-white"><?= htmlspecialchars($link['name']) ?></a>
                        <?php endforeach; 
                    endif;
                    ?>
                </div>
                <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($data['settings']['site_name'] ?? 'GameHub') ?>. All Rights Reserved.</p>
                <p class="text-sm">This website is for entertainment purposes only. All games are the property of their respective owners.</p>
            </div>
        </footer>
    </div>
</body>
</html>

