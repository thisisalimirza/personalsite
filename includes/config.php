<?php
session_start();
define('ADMIN_USERNAME', 'alimirza'); // Changed to your name for better security
define('ADMIN_PASSWORD_HASH', password_hash('REPLACE_WITH_A_STRONG_PASSWORD', PASSWORD_DEFAULT)); // You'll need to change this password
define('POSTS_DIR', __DIR__ . '/../content/posts/');
define('SITE_URL', 'https://' . $_SERVER['HTTP_HOST']); // Automatically detect the domain

// Security headers
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self' https: 'unsafe-inline' 'unsafe-eval' cdnjs.cloudflare.com");

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Function to sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to generate URL-friendly slug
function generateSlug($title) {
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

// Ensure posts directory exists
if (!is_dir(POSTS_DIR)) {
    mkdir(POSTS_DIR, 0755, true);
}
?> 