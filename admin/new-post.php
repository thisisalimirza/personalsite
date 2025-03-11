<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    
    if (empty($title) || empty($content)) {
        $error = 'Title and content are required';
    } else {
        $date = date('Y-m-d');
        $slug = generateSlug($title);
        $filename = $date . '-' . $slug . '.md';
        
        // Create front matter
        $frontMatter = <<<EOT
---
title: $title
date: $date
slug: $slug
---

$content
EOT;

        if (file_put_contents(POSTS_DIR . $filename, $frontMatter)) {
            $success = 'Post created successfully!';
            // Redirect after a brief delay
            header("refresh:2;url=index.php");
        } else {
            $error = 'Failed to create post';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Post - Ali Mirza</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
    <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
    <style>
        .editor-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
        }
        .success-message {
            color: #28a745;
            margin-bottom: 15px;
        }
        .button-group {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            color: white;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="editor-container">
        <h2>New Post</h2>
        
        <?php if ($error): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <p class="success-message"><?php echo $success; ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" name="content"></textarea>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-primary">Publish</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        var simplemde = new SimpleMDE({
            element: document.getElementById("content"),
            spellChecker: true,
            autosave: {
                enabled: true,
                unique_id: "new_post"
            }
        });
    </script>
</body>
</html> 