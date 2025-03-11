<?php
require_once 'includes/config.php';

// Get list of posts
$posts = [];
if (is_dir(POSTS_DIR)) {
    $files = glob(POSTS_DIR . '*.md');
    foreach ($files as $file) {
        $content = file_get_contents($file);
        preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)/s', $content, $matches);
        
        if (count($matches) >= 3) {
            $frontMatter = [];
            foreach (explode("\n", $matches[1]) as $line) {
                if (strpos($line, ':') !== false) {
                    list($key, $value) = explode(':', $line, 2);
                    $frontMatter[trim($key)] = trim($value);
                }
            }
            
            // Get excerpt (first 200 characters)
            $excerpt = substr(strip_tags($matches[2]), 0, 200) . '...';
            
            $posts[] = [
                'title' => $frontMatter['title'] ?? basename($file, '.md'),
                'date' => $frontMatter['date'] ?? 'Unknown',
                'slug' => $frontMatter['slug'] ?? basename($file, '.md'),
                'excerpt' => $excerpt
            ];
        }
    }
}

// Sort posts by date (newest first)
usort($posts, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Essays - Ali Mirza</title>
    <link rel="shortcut icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .essays-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .essay-card {
            margin-bottom: 40px;
            padding: 20px;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }
        .essay-card:hover {
            transform: translateY(-2px);
        }
        .essay-title {
            margin: 0 0 10px 0;
            color: #333;
        }
        .essay-date {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 15px;
        }
        .essay-excerpt {
            color: #444;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .read-more {
            display: inline-block;
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }
        .read-more:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <nav class="main-nav">
        <div class="nav-container">
            <a href="index.html" class="nav-link">Home</a>
            <a href="resources.html" class="nav-link">Resources</a>
            <a href="meditations.html" class="nav-link">Meditations</a>
            <a href="essays.php" class="nav-link active">Thoughts & Essays</a>
        </div>
    </nav>

    <div class="essays-container">
        <h1>Thoughts & Essays</h1>
        
        <?php if (empty($posts)): ?>
            <p>No essays published yet.</p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <article class="essay-card">
                    <h2 class="essay-title"><?php echo htmlspecialchars($post['title']); ?></h2>
                    <div class="essay-date"><?php echo htmlspecialchars($post['date']); ?></div>
                    <p class="essay-excerpt"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                    <a href="essays/<?php echo htmlspecialchars($post['slug']); ?>.html" class="read-more">
                        Read more →
                    </a>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <footer>
        <nav>
            <a href="#blog">Blog</a>
            <a href="#jobs">Jobs</a>
            <a href="#contact">Contact</a>
        </nav>
        <p class="copyright">© 2024 Ali Mirza. All rights reserved.</p>
    </footer>
</body>
</html> 