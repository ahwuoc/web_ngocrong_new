<?php
session_start();

// Cấu hình
define('SITE_URL', 'http://localhost/xampp/htdocs/');
define('ASSETS_URL', SITE_URL . 'assets/');
define('UPLOADS_URL', SITE_URL . 'uploads/');

// Bao gồm file database
require_once __DIR__ . '/../config/database.php';

// Hàm tạo URL thân thiện
function get_category_url($slug) {
    return $slug . '/';
}

function get_post_url($slug) {
    return 'post/' . $slug . '/';
}

function get_category_page_url($slug, $page) {
    if ($page <= 1) {
        return get_category_url($slug);
    }
    return $slug . '/page/' . $page . '/';
}

// Hàm tiện ích
function redirect($url) {
    header("Location: " . $url);
    exit();
}

function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function get_logged_in_user() {
    if (!is_logged_in()) {
        return null;
    }
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM account WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function is_admin() {
    $user = get_logged_in_user();
    return $user && $user['is_admin'] == 1;
}

function get_setting($key, $default = '') {
    global $pdo;
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE key_name = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['value'] : $default;
}

function get_posts_by_category($category_slug, $limit = 5) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name
        FROM posts p
        JOIN categories c ON p.category_id = c.id
        JOIN account a ON p.author_id = a.id
        WHERE c.slug = ? AND p.status = 'published'
        ORDER BY p.published_at DESC
        LIMIT ?
    ");
    $stmt->bindValue(1, $category_slug, PDO::PARAM_STR);
    $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_all_slides() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM slides WHERE status = 'active' ORDER BY sort_order ASC");
    $stmt->execute();
    return $stmt->fetchAll();
}

function create_slug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

function format_date($date) {
    return date('d.m', strtotime($date));
}

function format_datetime($date) {
    return date('d/m/Y H:i', strtotime($date));
}

// Xử lý lỗi
function show_error($message) {
    $_SESSION['error'] = $message;
}

function show_success($message) {
    $_SESSION['success'] = $message;
}

function get_flash_message() {
    $message = '';
    if (isset($_SESSION['error'])) {
        $message = '<div class="alert alert-error">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        $message = '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
        unset($_SESSION['success']);
    }
    return $message;
}
?>
