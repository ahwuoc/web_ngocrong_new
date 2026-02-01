<?php
require_once 'includes/functions.php';

// Nếu đã đăng nhập thì chuyển về trang chủ
if (is_logged_in()) {
    redirect('/');
}

// Xử lý đăng nhập
// ...existing code...

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($username) || empty($password)) {
        show_error('Vui lòng nhập đầy đủ thông tin.');
    } else {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM account WHERE (username = ? OR email = ?) LIMIT 1");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            show_success('Đăng nhập thành công!');
            redirect('/');
        } else {
            show_error('Sai tài khoản hoặc mật khẩu.');
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
    <title>Đăng nhập - <?= get_setting('site_name') ?></title>
</head>
<body>
    <div class="main-content">
        <div class="container">
            <div class="breadcrumb">
                <a href="/">Trang chủ</a> &gt; <span>Đăng nhập</span>
            </div>
            <div class="post-detail" style="max-width:400px;margin:40px auto;">
                <header class="post-header">
                    <h1 class="post-title" style="font-family:'Bangers',cursive;font-size:2em;text-align:center;">Đăng nhập</h1>
                </header>
                <?= get_flash_message() ?>
                <form method="post" class="post-form" style="margin-top:20px;">
                    <div class="form-group">
                        <label for="username" class="post-label">Tài khoản hoặc Email</label>
                        <input type="text" name="username" id="username" class="post-input" required>
                    </div>
                    <div class="form-group">
                        <label for="password" class="post-label">Mật khẩu</label>
                        <input type="password" name="password" id="password" class="post-input" required>
                    </div>
                    <!-- Xóa captcha -->
                    <button type="submit" class="btn btn-primary" style="width:100%;margin-top:10px;">Đăng nhập</button>
                    <p style="text-align:center;margin-top:15px;font-weight:bold;color:#444;">Bạn chưa có tài khoản? <a href="/register" style="font-weight:bold;color:#007bff;">Đăng ký ngay</a></p>
                </form>
            </div>
        </div>
    </div>
    <script src="/assets/frontend/home/v1/js/jquery.min.js"></script>
    <script src="/assets/js/main.js"></script>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
