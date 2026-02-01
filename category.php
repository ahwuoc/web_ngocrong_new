<?php
require_once 'includes/functions.php';

// Lấy slug từ URL
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    redirect('/');
}

// Lấy thông tin category
$stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ? AND status = 'active'");
$stmt->execute([$slug]);
$category = $stmt->fetch();

if (!$category) {
    redirect('/');
}

// Phân trang
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Đếm tổng số bài viết
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM posts p
    WHERE p.category_id = ? AND p.status = 'published'
");
$stmt->execute([$category['id']]);
$total_posts = $stmt->fetchColumn();
$total_pages = ceil($total_posts / $per_page);

// Lấy danh sách bài viết
$stmt = $pdo->prepare("
    SELECT p.*
    FROM posts p
    JOIN account a ON p.author_id = a.id
    WHERE p.category_id = ? AND p.status = 'published'
    ORDER BY p.published_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $category['id'], PDO::PARAM_INT);
$stmt->bindValue(2, (int)$per_page, PDO::PARAM_INT);
$stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($category['name']) ?> - <?= get_setting('site_name') ?></title>
    <link rel="shortcut icon" type="ico" href="/favicon.ico" />
    <link rel="stylesheet" href="/assets/frontend/home/v1/css/stylea6ca.css?v=919" />
    <link rel="stylesheet" href="/assets/css/category.css" />
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <div class="breadcrumb">
                <a href="/">Trang chủ</a> > <span><?= escape($category['name']) ?></span>
            </div>
            
            <div class="category-header">
                <h1><?= escape($category['name']) ?></h1>
                <?php if ($category['description']): ?>
                    <p class="category-description"><?= escape($category['description']) ?></p>
                <?php endif; ?>
            </div>
            
            <div class="posts-grid<?= empty($posts) ? ' empty-grid' : '' ?>">
                <?php if (empty($posts)): ?>
                    <div class="no-posts">
                        <p>Chưa có bài viết nào trong danh mục này.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="post-item">
                            <div class="post-image">
                                <?php if ($post['featured_image']): ?>
                                    <a href="/post/<?= escape($post['slug']) ?>">
                                        <img src="<?= escape($post['featured_image']) ?>" alt="<?= escape($post['title']) ?>">
                                    </a>
                                <?php else: ?>
                                    <a href="/post/<?= escape($post['slug']) ?>">
                                        <img src="assets/images/default-thumbnail.jpg" alt="<?= escape($post['title']) ?>">
                                    </a>
                                <?php endif; ?>
                            </div>
                            
                            <div class="post-content">
                                <h2 class="post-title">
                                    <a href="/post/<?= escape($post['slug']) ?>"><?= escape($post['title']) ?></a>
                                </h2>
                                
                                <div class="post-meta">
                                    <span class="post-author">đăng bởi TuaansNe</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="/<?= escape($slug) ?>/page/<?= $page - 1 ?>" class="page-link">&laquo; Trang trước</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="page-link current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="/<?= escape($slug) ?>/page/<?= $i ?>" class="page-link"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="/<?= escape($slug) ?>/page/<?= $page + 1 ?>" class="page-link">Trang sau &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="/assets/frontend/home/v1/js/jquery.min.js"></script>
    <script src="/assets/js/main.js"></script>
</body>
</html>
</html>
</html>
