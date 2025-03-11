<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];
    $files = glob(POSTS_DIR . '*' . $slug . '.md');
    
    if (!empty($files)) {
        $file = $files[0];
        $html_file = "../essays/{$slug}.html";
        
        // Delete both Markdown and HTML files
        unlink($file);
        if (file_exists($html_file)) {
            unlink($html_file);
        }
        
        header('Location: index.php?message=Post deleted successfully');
        exit;
    }
}

header('Location: index.php?error=Post not found');
exit; 