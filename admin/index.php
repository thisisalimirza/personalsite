<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

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
            
            $posts[] = [
                'title' => $frontMatter['title'] ?? basename($file, '.md'),
                'date' => $frontMatter['date'] ?? 'Unknown',
                'filename' => basename($file)
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
    <title>Admin Dashboard - Ali Mirza</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .posts-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .posts-table th, .posts-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .posts-table th {
            background-color: #f8f9fa;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            cursor: pointer;
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
        .logout-btn {
            color: #dc3545;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h2>Blog Posts</h2>
            <div>
                <a href="new-post.php" class="btn btn-primary">New Post</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <table class="posts-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                    <td><?php echo htmlspecialchars($post['date']); ?></td>
                    <td class="action-buttons">
                        <a href="edit-post.php?file=<?php echo urlencode($post['filename']); ?>" class="btn btn-edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="delete-post.php?file=<?php echo urlencode($post['filename']); ?>" 
                           class="btn btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this post?')">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 