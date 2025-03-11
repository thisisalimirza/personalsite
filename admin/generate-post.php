<?php
require_once '../includes/config.php';
require_once 'Parsedown.php'; // You'll need to download this file from https://github.com/erusev/parsedown

function generatePost($markdownFile) {
    $content = file_get_contents($markdownFile);
    preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)/s', $content, $matches);
    
    if (count($matches) < 3) {
        return false;
    }

    // Parse front matter
    $frontMatter = [];
    foreach (explode("\n", $matches[1]) as $line) {
        if (strpos($line, ':') !== false) {
            list($key, $value) = explode(':', $line, 2);
            $frontMatter[trim($key)] = trim($value);
        }
    }

    // Parse markdown content
    $Parsedown = new Parsedown();
    $htmlContent = $Parsedown->text($matches[2]);

    // Generate HTML
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$frontMatter['title']} - Ali Mirza</title>
    <link rel="shortcut icon" type="image/png" href="../favicon.png">
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .post-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .post-header {
            margin-bottom: 40px;
            text-align: center;
        }
        .post-date {
            color: #666;
            margin-top: 10px;
        }
        .post-content {
            line-height: 1.8;
            color: #333;
        }
        .post-content h1,
        .post-content h2,
        .post-content h3 {
            margin-top: 2em;
        }
        .post-content p {
            margin: 1.5em 0;
        }
        .post-content img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 2em auto;
        }
        .post-content blockquote {
            border-left: 4px solid #007bff;
            margin: 1.5em 0;
            padding-left: 1em;
            color: #555;
        }
        .post-content code {
            background: #f8f9fa;
            padding: 0.2em 0.4em;
            border-radius: 3px;
            font-size: 0.9em;
        }
        .post-content pre code {
            display: block;
            padding: 1em;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <nav class="main-nav">
        <div class="nav-container">
            <a href="../index.html" class="nav-link">Home</a>
            <a href="../resources.html" class="nav-link">Resources</a>
            <a href="../meditations.html" class="nav-link">Meditations</a>
            <a href="../essays.php" class="nav-link active">Thoughts & Essays</a>
        </div>
    </nav>

    <article class="post-container">
        <header class="post-header">
            <h1>{$frontMatter['title']}</h1>
            <div class="post-date">{$frontMatter['date']}</div>
        </header>

        <div class="post-content">
            $htmlContent
        </div>
    </article>

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
HTML;

    // Create essays directory if it doesn't exist
    if (!is_dir('../essays')) {
        mkdir('../essays', 0755, true);
    }

    // Save the HTML file
    $htmlFile = '../essays/' . $frontMatter['slug'] . '.html';
    return file_put_contents($htmlFile, $html);
}

// Generate HTML for a specific post if file parameter is provided
if (isset($_GET['file'])) {
    $markdownFile = POSTS_DIR . $_GET['file'];
    if (file_exists($markdownFile)) {
        if (generatePost($markdownFile)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Failed to generate post']);
        }
    } else {
        echo json_encode(['error' => 'File not found']);
    }
}

// Generate HTML for all posts if no file parameter
else {
    $files = glob(POSTS_DIR . '*.md');
    $results = [];
    
    foreach ($files as $file) {
        $filename = basename($file);
        $results[$filename] = generatePost($file);
    }
    
    echo json_encode(['results' => $results]);
}
?> 