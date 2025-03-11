<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ali Mirza</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        .posts-list {
            margin-top: 20px;
        }
        .post-item {
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .post-title {
            margin: 0;
            font-size: 1.1em;
        }
        .post-date {
            color: #666;
            font-size: 0.9em;
        }
        .post-actions {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            color: white;
            text-decoration: none;
            font-size: 0.9em;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-edit {
            background-color: #28a745;
        }
        .message {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .message-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="header">
            <h1>Admin Dashboard</h1>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <?php if ($message): ?>
            <div class="message message-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message message-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="actions">
            <a href="new-post.php" class="btn btn-primary">New Post</a>
        </div>

        <div class="posts-list">
            <h2>Your Posts</h2>
            <?php
            $posts = glob(POSTS_DIR . '*.md');
            
            if (empty($posts)) {
                echo '<p>No posts yet.</p>';
            } else {
                usort($posts, function($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                
                foreach ($posts as $post) {
                    $content = file_get_contents($post);
                    $filename = basename($post, '.md');
                    
                    // Extract title from the first line
                    $lines = explode("\n", $content);
                    $title = trim(str_replace('#', '', $lines[0]));
                    $date = date('F j, Y', filemtime($post));
                    
                    // Get the slug (remove date prefix if it exists)
                    $slug = preg_replace('/^\d{4}-\d{2}-\d{2}-/', '', $filename);
                    
                    echo '<div class="post-item">';
                    echo '<div class="post-info">';
                    echo '<h3 class="post-title">' . htmlspecialchars($title) . '</h3>';
                    echo '<div class="post-date">' . htmlspecialchars($date) . '</div>';
                    echo '</div>';
                    echo '<div class="post-actions">';
                    echo '<a href="edit-post.php?slug=' . urlencode($slug) . '" class="btn btn-edit">Edit</a>';
                    echo '<a href="delete-post.php?slug=' . urlencode($slug) . '" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to delete this post?\')">Delete</a>';
                    echo '<a href="../essays/' . urlencode($slug) . '.html" target="_blank" class="btn btn-primary">View</a>';
                    echo '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
</body>
</html> 