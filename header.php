<?php
// header.php

// This file contains the opening HTML, the <head> section, and the main navigation header.
// It's included at the top of every page by the main index.php router.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9953925694976658"
     crossorigin="anonymous"></script>
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-5FX8PCZSZX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-5FX8PCZSZX');
</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> | <?= htmlspecialchars($data['settings']['site_name']) ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta_description) ?>">
    
    <!-- This line adds the custom favicon to your site. -->
    <!-- It checks if a favicon URL is set in the database and uses it if available. -->
    <?php if (!empty($data['settings']['favicon_url'])): ?>
        <link rel="icon" href="<?= htmlspecialchars($data['settings']['favicon_url']) ?>">
    <?php endif; ?>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #121212; color: #e0e0e0; }
        .font-orbitron { font-family: 'Orbitron', sans-serif; }
        .bg-grid { background-image: linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px); background-size: 20px 20px; }
        .neon-text { text-shadow: 0 0 5px #00aaff, 0 0 10px #00aaff, 0 0 15px #00aaff; }
        .card-hover-effect { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card-hover-effect:hover { transform: translateY(-8px); box-shadow: 0 10px 20px rgba(0, 170, 255, 0.2); }
        .btn-primary { background: linear-gradient(90deg, #00aaff, #0055ff); transition: all 0.3s ease; }
        .btn-primary:hover { box-shadow: 0 0 15px #00aaff; transform: scale(1.05); }
        .admin-panel input, .admin-panel textarea, .admin-panel select, .admin-panel .checkbox-group { background-color: #2a2a2a; border: 1px solid #444; color: #e0e0e0; }
        .admin-panel input:focus, .admin-panel textarea:focus, .admin-panel select:focus { outline: none; border-color: #00aaff; box-shadow: 0 0 5px rgba(0, 170, 255, 0.5); }
        .content-box { background-color: rgba(26, 26, 26, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .content-box h1, .content-box h2 { font-family: 'Orbitron', sans-serif; }
        .content-box p { line-height: 1.7; }
        .content-box a { color: #00aaff; text-decoration: underline; }

        /* Styles for the smooth image fade-in effect */
        img.lazy { opacity: 0; transition: opacity 0.5s ease-in-out; }
        img.lazy.loaded { opacity: 1; }
        img.lazy-placeholder { filter: blur(10px); transform: scale(1.1); }
    </style>
</head>
<body class="bg-gray-900 text-gray-200">
    <!-- Main site wrapper -->
    <div class="flex flex-col min-h-screen">
        <header class="bg-black bg-opacity-50 backdrop-blur-md sticky top-0 z-50 shadow-lg shadow-cyan-500/10">
            <nav class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex-shrink-0">
                        <a href="/" class="text-2xl font-orbitron font-bold text-white neon-text"><?= htmlspecialchars($data['settings']['site_name']) ?></a>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <?php 
                            if (isset($data['settings']['header_links']) && is_array($data['settings']['header_links'])):
                                foreach ($data['settings']['header_links'] as $link): 
                                    $href = '#';
                                    if (isset($link['slug'])) {
                                        $href = $link['type'] === 'page' ? '/'.$link['slug'] : '/category/'.$link['slug'];
                                        if ($link['slug'] === 'home') $href = '/';
                                    }
                                ?>
                                    <a href="<?= $href ?>" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium"><?= htmlspecialchars($link['name']) ?></a>
                                <?php endforeach; 
                            endif;
                            ?>
                        </div>
                    </div>
                     <div class="flex items-center">
                        <?php if (is_admin()): ?>
                            <a href="/admin" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Admin Panel</a>
                            <a href="/logout" class="ml-4 text-gray-300 hover:bg-red-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Logout</a>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>
        </header>

        <main class="flex-grow">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

