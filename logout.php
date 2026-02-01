<?php
require_once 'includes/functions.php';

// Xóa session
session_destroy();

// Xóa remember token
if (isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $stmt = $pdo->prepare("DELETE FROM user_sessions WHERE session_token = ?");
    $stmt->execute([$token]);
    
    setcookie('remember_token', '', time() - 3600, '/');
}

// Redirect về trang chủ
redirect('/');
?>
