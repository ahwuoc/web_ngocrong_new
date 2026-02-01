<?php
require_once 'includes/functions.php';

// Yêu cầu đăng nhập
if (!is_logged_in()) {
    redirect('/login');
}

$user = get_logged_in_user();
if (!$user) {
    redirect('/login');
}

// Lấy thông tin player
global $pdo;
$stmt = $pdo->prepare("SELECT * FROM player WHERE account_id = ? LIMIT 1");
$stmt->execute([$user['id']]);
$player = $stmt->fetch();

// Hàm lấy avatar của player
function getPlayerAvatar($head) {
    global $pdo;
    
    if (empty($head)) {
        return 'assets/frontend/home/v1/images/bannergame.png'; // Default avatar
    }
    
    try {
        $stmt = $pdo->prepare("SELECT avatar_id FROM head_avatar WHERE head_id = ? LIMIT 1");
        $stmt->execute([$head]);
        $avatar = $stmt->fetch();
        
        if ($avatar && !empty($avatar['avatar_id'])) {
            return 'assets/frontend/home/v1/images/x1/' . $avatar['avatar_id'] . '.png';
        }
    } catch (Exception $e) {
        // Fallback if head_avatar table doesn't exist or query fails
    }
    
    return 'assets/frontend/home/v1/images/bannergame.png'; // Default avatar
}

// Hàm parse sức mạnh từ datapoint
function getPowerFromDatapoint($datapoint) {
    if (empty($datapoint)) {
        return 0;
    }
    
    // Parse JSON array
    $data = json_decode($datapoint, true);
    if (is_array($data) && count($data) > 1) {
        return number_format($data[1]); // Vị trí thứ 2 (index 1) là sức mạnh
    }
    
    return 0;
}

// Hàm parse thông tin khác từ datapoint (nếu cần)
function getDataFromDatapoint($datapoint, $index) {
    if (empty($datapoint)) {
        return 0;
    }
    
    $data = json_decode($datapoint, true);
    if (is_array($data) && count($data) > $index) {
        return number_format($data[$index]);
    }
    
    return 0;
}

// Hàm lấy tên nhiệm vụ từ data_task
function getTaskName($dataTask) {
    global $pdo;
    
    if (empty($dataTask)) {
        return 'Chưa có nhiệm vụ';
    }
    
    // Parse JSON array để lấy task ID (vị trí đầu tiên)
    $data = json_decode($dataTask, true);
    if (is_array($data) && count($data) > 0) {
        $taskId = $data[0];
        
        // Lấy tên nhiệm vụ từ bảng task_main_template
        $stmt = $pdo->prepare("SELECT name FROM task_main_template WHERE id = ? LIMIT 1");
        $stmt->execute([$taskId]);
        $task = $stmt->fetch();
        
        if ($task) {
            return $task['name'];
        }
    }
    
    return 'Nhiệm vụ không xác định';
}

// Xử lý đổi mật khẩu
if (isset($_POST['change_password'])) {
    $new_password = $_POST['new_password'] ?? '';
    if (empty($new_password)) {
        show_error('Vui lòng nhập mật khẩu mới.');
    } else {
        $stmt = $pdo->prepare("UPDATE account SET password = ? WHERE id = ?");
        $stmt->execute([$new_password, $user['id']]);
        show_success('Đổi mật khẩu thành công!');
        $user = get_logged_in_user();
    }
}

// Xử lý kích hoạt tài khoản
if (isset($_POST['activate_account']) && $user['active'] == 0) {
    $stmt = $pdo->prepare("UPDATE account SET active = 1 WHERE id = ?");
    $stmt->execute([$user['id']]);
    show_success('Kích hoạt tài khoản thành công!');
    $user = get_logged_in_user();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'includes/header.php'; ?>
    <link rel="stylesheet" href="/assets/frontend/home/v1/css/stylea6ca.css?v=919" />
    <link rel="stylesheet" href="/assets/css/post.css" />
    <title>Thông tin cá nhân - <?= get_setting('site_name') ?></title>
    <style>
        .profile-avatar { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; }
        .profile-form { max-width:400px; margin:40px auto; background:#fff; border-radius:10px; box-shadow:0 2px 8px #eee; padding:30px; }
        
        /* Mobile responsive cho các nút */
        @media (max-width: 768px) {
            .button-grid-2x2 {
                display: flex !important;
                flex-direction: column !important;
                gap: 10px !important;
            }
            
            .button-grid-1x3 {
                display: flex !important;
                flex-direction: column !important;
                gap: 10px !important;
            }
            
            .profile-container {
                max-width: 95% !important;
                margin: 15px auto !important;
            }
            
            .account-info-section {
                flex-direction: row !important;
                align-items: flex-start !important;
                text-align: left !important;
                gap: 15px !important;
            }
            
            .account-info-section img {
                width: 60px !important;
                height: 60px !important;
                margin-top: 18px !important;
            }
            
            .account-info-section > div:last-child {
                padding-left: 10px !important;
                flex: 1 !important;
            }
            
            .account-info-section > div:last-child > div {
                text-align: left !important;
                display: flex !important;
                align-items: center !important;
                justify-content: flex-start !important;
                margin-bottom: 5px !important;
                flex-wrap: wrap !important;
            }
            
            .account-info-section > div:last-child > div:first-child {
                font-size: 1.3em !important;
            }
            
            .account-info-section > div:last-child > div:nth-child(2),
            .account-info-section > div:last-child > div:nth-child(3) {
                font-size: 1.1em !important;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="container">
            <div class="breadcrumb">
                <a href="/">Trang chủ</a> &gt; <span>Thông tin cá nhân</span>
            </div>
            <div class="profile-container" style="max-width:600px;margin:30px auto;">
                <!-- 1. Tiêu đề -->
                <div style="background:#fff;border:3px solid #222;border-radius:12px;padding:20px;margin-bottom:20px;text-align:center;box-shadow:0 4px 8px rgba(0,0,0,0.2);">
                    <h1 style="font-family:'Bangers',cursive;font-size:2.5em;color:#222;text-shadow:2px 2px 0 #fff;letter-spacing:2px;margin:0;text-transform:uppercase;">THÔNG TIN TÀI KHOẢN</h1>
                </div>
                
                <?= get_flash_message() ?>
                
                <!-- 2. Khối 1 - Thông tin account -->
                <div class="account-info-section" style="background:#d597fa;border:3px solid #222;border-radius:12px;padding:20px;margin-bottom:20px;display:flex;align-items:center;gap:20px;box-shadow:0 4px 8px rgba(0,0,0,0.2);">
                    <div style="flex-shrink:0;">
                        <img src="<?= getPlayerAvatar($player['head'] ?? null) ?>" alt="avatar" style="width:80px;height:80px;border-radius:50%;border:3px solid #222;object-fit:cover;">
                    </div>
                    <div style="flex:1;">
                        <div style="font-family:'Bangers',cursive;font-size:1.5em;color:#222;margin-bottom:8px;text-shadow:1px 1px 0 #fff;">👤 <?= escape($user['username']) ?></div>
                        <div style="font-family:'Bangers',cursive;font-size:1.2em;color:#444;margin-bottom:8px;">📧 <?= !empty($user['email']) ? escape($user['email']) : 'Chưa cập nhật email' ?></div>
                        <div style="font-family:'Bangers',cursive;font-size:1.2em;color:#444;">💰 Số dư: <span style="color:#fff;font-weight:bold;text-shadow:2px 2px 0 #222;background:#ff6b35;padding:3px 8px;border-radius:5px;border:2px solid #222;margin-left:5px;"><?= number_format($user['danap'] ?? 0) ?> VNĐ</span></div>
                    </div>
                </div>
                
                <!-- 3. Khối 2 - Thông tin nhân vật/game -->
                <?php if ($player): ?>
                <div style="background:#90cdf4;border:3px solid #222;border-radius:12px;padding:20px;margin-bottom:20px;box-shadow:0 4px 8px rgba(0,0,0,0.2);">
                    <h2 style="font-family:'Bangers',cursive;font-size:1.8em;color:#1a202c;text-shadow:1px 1px 0 #fff;margin:0 0 15px 0;text-align:center;">🎮 THÔNG TIN NHÂN VẬT</h2>
                    <div style="text-align:left;max-width:400px;margin:0 auto;">
                        <div style="font-family:'Bangers',cursive;font-size:1.3em;color:#1a202c;margin-bottom:8px;display:flex;align-items:center;">
                            <span style="width:140px;display:inline-block;">🏷️ Tên:</span>
                            <span style="color:#1a202c;font-weight:bold;"><?= escape($player['name']) ?></span>
                        </div>
                        <div style="font-family:'Bangers',cursive;font-size:1.3em;color:#1a202c;margin-bottom:8px;display:flex;align-items:center;">
                            <span style="width:140px;display:inline-block;">💪 Sức mạnh:</span>
                            <span style="color:#fff;font-weight:bold;text-shadow:2px 2px 0 #222;background:#ff6b35;padding:3px 8px;border-radius:5px;border:2px solid #222;"><?= getPowerFromDatapoint($player['data_point']) ?></span>
                        </div>
                        <div style="font-family:'Bangers',cursive;font-size:1.3em;color:#1a202c;margin-bottom:8px;display:flex;align-items:center;">
                            <span style="width:140px;display:inline-block;">🎯 Nhiệm vụ:</span>
                            <span style="color:#1a202c;font-weight:bold;"><?= getTaskName($player['data_task']) ?></span>
                        </div>
                        <div style="font-family:'Bangers',cursive;font-size:1.3em;color:#1a202c;margin-bottom:8px;display:flex;align-items:center;">
                            <span style="width:140px;display:inline-block;">✅ Trạng thái:</span>
                            <span style="color:<?= $user['active'] ? '#16a34a' : '#dc2626' ?>;font-weight:bold;"><?= $user['active'] ? 'ĐÃ KÍCH HOẠT' : 'CHƯA KÍCH HOẠT' ?></span>
                        </div>
                        <div style="font-family:'Bangers',cursive;font-size:1.3em;color:#1a202c;display:flex;align-items:center;">
                            <span style="width:140px;display:inline-block;">🌍 Hành tinh:</span>
                            <span style="color:#1a202c;font-weight:bold;"><?= ($player['gender'] == 0 ? 'Trái Đất' : ($player['gender'] == 1 ? 'Namec' : 'Xayda')) ?></span>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div style="background:#90cdf4;border:3px solid #222;border-radius:12px;padding:20px;margin-bottom:20px;box-shadow:0 4px 8px rgba(0,0,0,0.2);text-align:center;">
                    <h2 style="font-family:'Bangers',cursive;font-size:1.8em;color:#1a202c;text-shadow:1px 1px 0 #fff;margin:0 0 15px 0;">🎮 THÔNG TIN NHÂN VẬT</h2>
                    <div style="font-family:'Bangers',cursive;font-size:1.5em;color:#1a202c;font-weight:bold;">⚠️ TÀI KHOẢN NÀY CHƯA TẠO NHÂN VẬT</div>
                </div>
                <?php endif; ?>
                
                <!-- 4. Khối 3 - Các nút chức năng -->
                <div style="background:#42e4f5;border:3px solid #222;border-radius:12px;padding:20px;box-shadow:0 4px 8px rgba(0,0,0,0.2);">
                    <?php if ($user['active'] == 0): ?>
                    <!-- Grid 2x2 khi chưa kích hoạt -->
                    <div class="button-grid-2x2" style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                        <!-- Đổi mật khẩu (trái trên) -->
                        <button id="changePasswordBtn" onclick="togglePasswordForm()" style="padding:15px;font-family:'Bangers',cursive;font-size:1.2em;background:#fff;color:#222;border:3px solid #222;border-radius:10px;cursor:pointer;transition:all 0.3s;text-shadow:none;box-shadow:0 3px 6px rgba(0,0,0,0.3);text-transform:uppercase;">🔒 Đổi mật khẩu</button>
                        
                        <!-- Kích hoạt (phải trên) -->
                        <form method="post" style="margin:0;">
                            <button type="submit" name="activate_account" style="width:100%;padding:15px;font-family:'Bangers',cursive;font-size:1.2em;background:#ff6b6b;color:#fff;border:3px solid #222;border-radius:10px;cursor:pointer;transition:all 0.3s;text-shadow:1px 1px 0 #222;box-shadow:0 3px 6px rgba(0,0,0,0.3);text-transform:uppercase;">⚡ Kích hoạt</button>
                        </form>
                        
                        <!-- Đăng xuất (trái dưới) -->
                        <button onclick="window.location='/logout'" style="padding:15px;font-family:'Bangers',cursive;font-size:1.2em;background:#fff;color:#222;border:3px solid #222;border-radius:10px;cursor:pointer;transition:all 0.3s;text-shadow:none;box-shadow:0 3px 6px rgba(0,0,0,0.3);text-transform:uppercase;">🚪 Đăng xuất</button>
                        
                        <!-- Nạp tiền (phải dưới) -->
                        <button onclick="window.location='napthe.php'" style="padding:15px;font-family:'Bangers',cursive;font-size:1.2em;background:#fff;color:#222;border:3px solid #222;border-radius:10px;cursor:pointer;transition:all 0.3s;text-shadow:none;box-shadow:0 3px 6px rgba(0,0,0,0.3);text-transform:uppercase;">💰 Nạp tiền</button>
                    </div>
                    <?php else: ?>
                    <!-- Grid 1x3 khi đã kích hoạt -->
                    <div class="button-grid-1x3" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:15px;">
                        <!-- Đổi mật khẩu -->
                        <button id="changePasswordBtn" onclick="togglePasswordForm()" style="padding:15px;font-family:'Bangers',cursive;font-size:1.2em;background:#fff;color:#222;border:3px solid #222;border-radius:10px;cursor:pointer;transition:all 0.3s;text-shadow:none;box-shadow:0 3px 6px rgba(0,0,0,0.3);text-transform:uppercase;">🔒 Đổi mật khẩu</button>
                        
                        <!-- Đăng xuất -->
                        <button onclick="window.location='/logout'" style="padding:15px;font-family:'Bangers',cursive;font-size:1.2em;background:#fff;color:#222;border:3px solid #222;border-radius:10px;cursor:pointer;transition:all 0.3s;text-shadow:none;box-shadow:0 3px 6px rgba(0,0,0,0.3);text-transform:uppercase;">🚪 Đăng xuất</button>
                        
                        <!-- Nạp tiền -->
                        <button onclick="window.location='napthe.php'" style="padding:15px;font-family:'Bangers',cursive;font-size:1.2em;background:#fff;color:#222;border:3px solid #222;border-radius:10px;cursor:pointer;transition:all 0.3s;text-shadow:none;box-shadow:0 3px 6px rgba(0,0,0,0.3);text-transform:uppercase;">💰 Nạp tiền</button>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Form đổi mật khẩu -->
                    <div id="passwordForm" style="display:none;margin-top:20px;padding:20px;background:#fff;border:3px solid #222;border-radius:10px;">
                        <form method="post">
                            <div style="margin-bottom:15px;">
                                <label for="new_password" style="font-family:'Bangers',cursive;font-size:1.2em;color:#222;display:block;margin-bottom:8px;">Mật khẩu mới:</label>
                                <input type="password" name="new_password" id="new_password" placeholder="Nhập mật khẩu mới" style="width:100%;border-radius:8px;border:3px solid #222;padding:12px;font-size:1em;box-sizing:border-box;" required>
                            </div>
                            <div style="margin-bottom:15px;">
                                <label for="confirm_password" style="font-family:'Bangers',cursive;font-size:1.2em;color:#222;display:block;margin-bottom:8px;">Xác nhận mật khẩu:</label>
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="Nhập lại mật khẩu mới" style="width:100%;border-radius:8px;border:3px solid #222;padding:12px;font-size:1em;box-sizing:border-box;" required>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                                <button type="submit" name="change_password" style="padding:12px;font-family:'Bangers',cursive;font-size:1.1em;background:#28a745;color:#fff;border:3px solid #222;border-radius:8px;cursor:pointer;text-transform:uppercase;">✅ Cập nhật</button>
                                <button type="button" onclick="togglePasswordForm()" style="padding:12px;font-family:'Bangers',cursive;font-size:1.1em;background:#dc3545;color:#fff;border:3px solid #222;border-radius:8px;cursor:pointer;text-transform:uppercase;">❌ Hủy</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/assets/frontend/home/v1/js/jquery.min.js"></script>
    <script src="/assets/js/main.js"></script>
    <script>
        function togglePasswordForm() {
            const form = document.getElementById('passwordForm');
            const btn = document.getElementById('changePasswordBtn');
            
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
                btn.innerHTML = '🔒 ĐANG ĐỔI MẬT KHẨU';
                btn.style.background = '#dc3545';
                btn.style.color = '#fff';
            } else {
                form.style.display = 'none';
                btn.innerHTML = '🔒 ĐỔI MẬT KHẨU';
                btn.style.background = '#fff';
                btn.style.color = '#222';
                // Reset form
                document.getElementById('new_password').value = '';
                document.getElementById('confirm_password').value = '';
            }
        }
        
        // Validate password confirmation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPass = document.getElementById('new_password').value;
            const confirmPass = this.value;
            
            if (newPass !== confirmPass) {
                this.style.borderColor = '#dc3545';
                this.style.background = '#ffe6e6';
            } else {
                this.style.borderColor = '#28a745';
                this.style.background = '#e6ffe6';
            }
        });
    </script>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
