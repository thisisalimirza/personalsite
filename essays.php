<?php
require_once 'includes/config.php';
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
    
    <div class="container">
        <h1>Thoughts & Essays</h1>
        <div class="essays-list">
            <?php
            // Get all posts
            $posts = glob(POSTS_DIR . '*.md');
            
            if (empty($posts)) {
                echo '<p>No essays published yet.</p>';
            } else {
                // Sort posts by date (newest first)
                usort($posts, function($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                
                foreach ($posts as $post) {
                    $content = file_get_contents($post);
                    $filename = basename($post, '.md');
                    
                    // Extract title and date from the first line (assuming it's in the format: # Title)
                    $lines = explode("\n", $content);
                    $title = trim(str_replace('#', '', $lines[0]));
                    $date = date('F j, Y', filemtime($post));
                    
                    // Get the slug (remove date prefix if it exists)
                    $slug = preg_replace('/^\d{4}-\d{2}-\d{2}-/', '', $filename);
                    
                    echo '<article class="essay-preview">';
                    echo '<h2><a href="essays/' . htmlspecialchars($slug) . '.html">' . htmlspecialchars($title) . '</a></h2>';
                    echo '<div class="essay-meta">' . htmlspecialchars($date) . '</div>';
                    echo '</article>';
                }
            }
            ?>
        </div>
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