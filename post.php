<?php
require_once 'includes/functions.php';

// Lấy slug từ URL
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    redirect('/');
}

// Lấy thông tin bài viết
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name, c.slug as category_slug
    FROM posts p
    JOIN categories c ON p.category_id = c.id
    JOIN account u ON p.author_id = u.id
    WHERE p.slug = ? AND p.status = 'published'
");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    redirect('/');
}

// Cập nhật lượt xem
$stmt = $pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?");
$stmt->execute([$post['id']]);

// Lấy bài viết liên quan
$stmt = $pdo->prepare("
    SELECT p.*
    FROM posts p
    JOIN account u ON p.author_id = u.id
    WHERE p.category_id = ? AND p.id != ? AND p.status = 'published'
    ORDER BY p.created_at DESC
    LIMIT 5
");
$stmt->execute([$post['category_id'], $post['id']]);
$related_posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($post['title']) ?> - <?= get_setting('site_name') ?></title>
    <link rel="shortcut icon" type="ico" href="/favicon.ico" />
    
    <meta name="description" content="<?= escape($post['excerpt']) ?>" />
    <meta property="og:title" content="<?= escape($post['title']) ?>" />
    <meta property="og:description" content="<?= escape($post['excerpt']) ?>" />
    <?php if ($post['featured_image']): ?>
        <meta property="og:image" content="<?= strpos(escape($post['featured_image']), '/') === 0 ? escape($post['featured_image']) : '/' . escape($post['featured_image']) ?>" />
    <?php endif; ?>
    
    <link rel="stylesheet" href="/assets/frontend/home/v1/css/stylea6ca.css?v=919" />
    <link rel="stylesheet" href="/assets/css/post.css" />
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <div class="breadcrumb">
                <a href="/">Trang chủ</a> > 
                <a href="/<?= escape($post['category_slug']) ?>"><?= escape($post['category_name']) ?></a> > 
                <span><?= escape($post['title']) ?></span>
            </div>
            
            <article class="post-detail">
                <header class="post-header">
                    <h1 class="post-title"><?= escape($post['title']) ?></h1>
                    
                    <div class="post-meta">
                        <span class="post-category">
                            <a href="/<?= escape($post['category_slug']) ?>"><?= escape($post['category_name']) ?></a>
                        </span>
                        <span class="post-author">đăng bởi <?= escape($post['username'] ?? 'Tài khoản') ?></span>
                        <span class="post-date"><?= format_datetime($post['created_at']) ?></span>
                        <span class="post-views"><?= number_format($post['views']) ?> lượt xem</span>
                    </div>
                </header>
                
                <?php if ($post['featured_image']): ?>
                    <div class="post-featured-image">
                        <img src="<?= strpos(escape($post['featured_image']), '/') === 0 ? escape($post['featured_image']) : '/' . escape($post['featured_image']) ?>" alt="<?= escape($post['title']) ?>">
                    </div>
                <?php endif; ?>
                
                <div class="post-content">
                    <?= $post['content'] ?>
                </div>
                
                <div class="post-actions">
                    <div class="social-share">
                        <span>Chia sẻ:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" target="_blank" class="share-facebook">Facebook</a>
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>&text=<?= urlencode($post['title']) ?>" target="_blank" class="share-twitter">Twitter</a>
                    </div>
                </div>
            </article>
            
            <!-- Related Posts -->
            <?php if (!empty($related_posts)): ?>
                <div class="related-posts">
                    <h3>Bài viết liên quan</h3>
                    <div class="related-posts-grid">
                        <?php foreach ($related_posts as $related): ?>
                            <div class="related-post-item">
                                <?php if ($related['featured_image']): ?>
                                    <div class="related-post-image">
                                        <a href="/post/<?= escape($related['slug']) ?>">
                                            <img src="<?= strpos(escape($related['featured_image']), '/') === 0 ? escape($related['featured_image']) : '/' . escape($related['featured_image']) ?>" alt="<?= escape($related['title']) ?>">
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="related-post-content">
                                    <h4 class="related-post-title">
                                        <a href="/post/<?= escape($related['slug']) ?>"><?= escape($related['title']) ?></a>
                                    </h4>
                                    
                                    <div class="related-post-meta">
                                        <span class="post-author">đăng bởi <?= escape($related['username'] ?? 'Tài khoản') ?></span>
                                        <span><?= format_datetime($related['created_at']) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
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
