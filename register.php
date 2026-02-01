<?php
require_once 'includes/functions.php';

// Nếu đã đăng nhập thì chuyển về trang chủ
if (is_logged_in()) {
    redirect('/');
}

// ...existing code...

// Xử lý đăng ký
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
        show_error('Vui lòng nhập đầy đủ thông tin.');
    } elseif ($password !== $confirm) {
        show_error('Mật khẩu xác nhận không khớp.');
    } else {
        global $pdo;
        // Kiểm tra username/email đã tồn tại
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM account WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetchColumn() > 0) {
            show_error('Tài khoản hoặc email đã tồn tại.');
        } else {
            $stmt = $pdo->prepare("INSERT INTO account (username, email, password, create_time, is_admin, ban, active) VALUES (?, ?, ?, NOW(), 0, 0, 1)");
            $stmt->execute([$username, $email, $password]);
            show_success('Đăng ký thành công! Bạn có thể đăng nhập.');
            redirect('/login');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'includes/header.php'; ?>
    <link rel="stylesheet" href="/assets/frontend/home/v1/css/stylea6ca.css?v=919" />
    <link rel="stylesheet" href="/assets/css/post.css" />
    <title>Đăng ký - <?= get_setting('site_name') ?></title>
</head>
<body>
    <div class="main-content">
        <div class="container">
            <div class="breadcrumb">
                <a href="/">Trang chủ</a> &gt; <span>Đăng ký</span>
            </div>
            <div class="post-detail" style="max-width:400px;margin:40px auto;">
                <header class="post-header">
                    <h1 class="post-title" style="font-family:'Bangers',cursive;font-size:2em;text-align:center;">Đăng ký</h1>
                </header>
                <?= get_flash_message() ?>
                <form method="post" class="post-form" style="margin-top:20px;">
                    <div class="form-group">
                        <label for="username" class="post-label">Tài khoản</label>
                        <input type="text" name="username" id="username" class="post-input" required>
                    </div>
                    <div class="form-group">
                        <label for="email" class="post-label">Email</label>
                        <input type="email" name="email" id="email" class="post-input" required>
                    </div>
                    <!-- Đã xóa trường họ tên -->
                    <div class="form-group">
                        <label for="password" class="post-label">Mật khẩu</label>
                        <input type="password" name="password" id="password" class="post-input" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm" class="post-label">Xác nhận mật khẩu</label>
                        <input type="password" name="confirm" id="confirm" class="post-input" required>
                    </div>
                    <!-- Xóa captcha -->
                    <button type="submit" class="btn btn-primary" style="width:100%;margin-top:10px;">Đăng ký</button>
                    <p style="text-align:center;margin-top:15px;font-weight:bold;color:#444;">Đã có tài khoản? <a href="/login" style="font-weight:bold;color:#007bff;">Đăng nhập</a></p>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/frontend/home/v1/js/jquery.min.js"></script>
    <script src="assets/js/main.js"></script>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
