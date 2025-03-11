<?php
session_start();
define('ADMIN_USERNAME', 'alimirza');
define('ADMIN_PASSWORD_HASH', password_hash('REPLACE_WITH_A_STRONG_PASSWORD', PASSWORD_DEFAULT));
define('POSTS_DIR', __DIR__ . '/../content/posts/');
define('SITE_URL', 'https://thisisalimirza.com');
define('ADMIN_URL', 'https://thisisalimirza.com/admin');

// GitHub API configuration
define('GITHUB_TOKEN', ''); // You'll add this through Hostinger's file manager
define('GITHUB_REPO', 'thisisalimirza/personal_website');
define('GITHUB_BRANCH', 'main');

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

// Function to push changes to GitHub
function pushToGitHub($file, $content, $message) {
    if (empty(GITHUB_TOKEN)) return false;
    
    $url = "https://api.github.com/repos/" . GITHUB_REPO . "/contents/" . $file;
    
    // Get the current file (if it exists) to get its SHA
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/vnd.github.v3+json',
        'Authorization: token ' . GITHUB_TOKEN,
        'User-Agent: PHP'
    ]);
    $result = curl_exec($ch);
    $fileInfo = json_decode($result, true);
    curl_close($ch);
    
    // Prepare the update data
    $data = [
        'message' => $message,
        'content' => base64_encode($content),
        'branch' => GITHUB_BRANCH
    ];
    
    if (isset($fileInfo['sha'])) {
        $data['sha'] = $fileInfo['sha'];
    }
    
    // Update or create the file
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/vnd.github.v3+json',
        'Authorization: token ' . GITHUB_TOKEN,
        'User-Agent: PHP'
    ]);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode >= 200 && $httpCode < 300;
}

// Ensure posts directory exists
if (!is_dir(POSTS_DIR)) {
    mkdir(POSTS_DIR, 0755, true);
}
?> 