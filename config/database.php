<?php
// Cấu hình cơ sở dữ liệu
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8');

// Tên database cho từng server
define('DB_NAME_SV1', getenv('DB_NAME_SV1') ?: 'nro_v1');
define('DB_NAME_SV2', getenv('DB_NAME_SV2') ?: 'nro_v2');

function connect_db($db_name) {
    try {
        return new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . $db_name . ";charset=" . DB_CHARSET,
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => true
            ]
        );
    } catch (PDOException $e) {
        die("Lỗi kết nối database ($db_name): " . $e->getMessage());
    }
}

// Khởi tạo các kết nối
$pdo1 = connect_db(DB_NAME_SV1);
$pdo2 = connect_db(DB_NAME_SV2);

// Biến $pdo mặc định (SV1) để giữ tương thích code cũ
$pdo = $pdo1;
?>
