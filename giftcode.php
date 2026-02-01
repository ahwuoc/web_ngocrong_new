<?php
require_once 'includes/functions.php';

global $pdo;

// Hàm lấy thông tin item từ item_template
function getItemInfo($temp_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT name, description, icon_id FROM item_template WHERE id = ? LIMIT 1");
        $stmt->execute([$temp_id]);
        $item = $stmt->fetch();
        
        if ($item) {
            return [
                'name' => $item['name'],
                'description' => $item['description'] ?? '',
                'icon_id' => $item['icon_id'] ?? ''
            ];
        }
    } catch (Exception $e) {
        // Fallback if item_template table doesn't exist
    }
    
    return [
        'name' => 'Item #' . $temp_id,
        'description' => '',
        'icon_id' => ''
    ];
}

// Hàm lấy thông tin option từ item_template_option
function getOptionInfo($option_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT name FROM item_template_option WHERE id = ? LIMIT 1");
        $stmt->execute([$option_id]);
        $option = $stmt->fetch();
        
        if ($option) {
            return $option['name'];
        }
    } catch (Exception $e) {
        // Fallback if item_template_option table doesn't exist
    }
    
    return 'Option #' . $option_id;
}

// Hàm parse detail của giftcode
function parseGiftcodeDetail($detail) {
    $items = [];
    
    if (empty($detail)) {
        return $items;
    }
    
    $data = json_decode($detail, true);
    if (is_array($data)) {
        foreach ($data as $item) {
            if (isset($item['temp_id']) && isset($item['quantity'])) {
                $itemInfo = getItemInfo($item['temp_id']);
                
                $items[] = [
                    'temp_id' => $item['temp_id'],
                    'name' => $itemInfo['name'],
                    'description' => $itemInfo['description'],
                    'icon_id' => $itemInfo['icon_id'],
                    'quantity' => $item['quantity']
                ];
            }
        }
    }
    
    return $items;
}

// Lấy danh sách giftcode còn hiệu lực
$stmt = $pdo->prepare("SELECT code, count_left, detail, expired, datecreate FROM giftcode WHERE count_left > 0 AND expired > NOW() ORDER BY datecreate DESC");
$stmt->execute();
$availableGiftcodes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'includes/header.php'; ?>
    <link rel="stylesheet" href="/assets/frontend/home/v1/css/stylea6ca.css?v=919" />
    <link rel="stylesheet" href="/assets/css/post.css" />
    <title>Giftcode - <?= get_setting('site_name') ?></title>
    <style>
        .giftcode-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 0 15px;
        }
        
        .main-title {
            font-family: 'Bangers', cursive;
            font-size: 2.5em;
            color: #222;
            text-shadow: 2px 2px 0 #fff;
            text-align: center;
            margin: 0 0 30px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            background: #fff;
            border: 3px solid #222;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .giftcode-list {
            background: #d597fa;
            border: 3px solid #222;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .giftcode-item {
            background: #fff;
            border: 3px solid #222;
            border-radius: 10px;
            margin-bottom: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .giftcode-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        }
        
        .giftcode-header {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            cursor: pointer;
            background: linear-gradient(45deg, #ff6b35, #ff8c42);
            color: #fff;
            border-bottom: 2px solid #222;
        }
        
        .gift-icon {
            width: 50px;
            height: 50px;
            background: url('assets/frontend/home/v1/images/bannergame.png') center/contain no-repeat;
            margin-right: 15px;
            border: 2px solid #222;
            border-radius: 8px;
            background-color: #fff;
        }
        
        .giftcode-info {
            flex: 1;
        }
        
        .giftcode-name {
            font-family: 'Bangers', cursive;
            font-size: 1.4em;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 2px 2px 0 #222;
            margin-bottom: 5px;
        }
        
        .giftcode-stats {
            font-family: 'Bangers', cursive;
            font-size: 1em;
            opacity: 0.9;
        }
        
        .expand-icon {
            width: 24px;
            height: 24px;
            background-image: url('assets/frontend/home/v1/images/arrow-right.png');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            transition: transform 0.3s ease;
        }
        
        .giftcode-item.expanded .expand-icon {
            transform: rotate(90deg);
        }
        
        .giftcode-details {
            display: none;
            padding: 20px;
            background: #f8f9fa;
            border-top: 2px solid #222;
        }
        
        .giftcode-item.expanded .giftcode-details {
            display: block;
        }
        
        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
        }
        
        .item-card {
            background: #fff;
            border: 2px solid #222;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .item-card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .item-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 8px;
            border: 2px solid #222;
            border-radius: 6px;
            background: #f0f0f0;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }
        
        .item-name {
            font-family: 'Bangers', cursive;
            font-size: 0.9em;
            color: #333;
            margin-bottom: 4px;
            word-wrap: break-word;
            line-height: 1.2;
        }
        
        .item-quantity {
            font-family: 'Bangers', cursive;
            font-size: 0.8em;
            color: #666;
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 10px;
            border: 1px solid #222;
        }
        
        .no-giftcodes {
            text-align: center;
            padding: 40px;
            font-family: 'Bangers', cursive;
            font-size: 1.4em;
            color: #666;
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            .giftcode-container {
                margin: 15px auto;
                padding: 0 10px;
            }
            
            .main-title {
                font-size: 2em;
                padding: 15px;
            }
            
            .giftcode-header {
                padding: 12px 15px;
            }
            
            .gift-icon {
                width: 40px;
                height: 40px;
            }
            
            .giftcode-name {
                font-size: 1.2em;
            }
            
            .items-grid {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
                gap: 10px;
            }
            
            .item-icon {
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <div class="breadcrumb">
                <a href="/">Trang chủ</a> > <span>Giftcode</span>
            </div>
            
            <div class="giftcode-container">
                <div class="main-title">
                    🎁 DANH SÁCH GIFTCODE
                </div>
                
                <!-- Danh sách giftcode -->
                <?php if (!empty($availableGiftcodes)): ?>
                <div class="giftcode-list">
                    <?php foreach ($availableGiftcodes as $index => $gc): ?>
                    <div class="giftcode-item" id="giftcode-<?= $index ?>">
                        <div class="giftcode-header" onclick="toggleGiftcode(<?= $index ?>)">
                            <div class="gift-icon"></div>
                            <div class="giftcode-info">
                                <div class="giftcode-name"><?= escape($gc['code']) ?></div>
                                <div class="giftcode-stats">
                                    Còn <?= $gc['count_left'] ?> lượt • Hết hạn <?= date('d/m/Y', strtotime($gc['expired'])) ?>
                                </div>
                            </div>
                            <div class="expand-icon"></div>
                        </div>
                        
                        <div class="giftcode-details">
                            <?php 
                            $gcItems = parseGiftcodeDetail($gc['detail']);
                            if (!empty($gcItems)): 
                            ?>
                            <div class="items-grid">
                                <?php foreach ($gcItems as $item): ?>
                                <div class="item-card">
                                    <div class="item-icon" style="background-image: url('assets/frontend/home/v1/images/x1/<?= !empty($item['icon_id']) ? escape($item['icon_id']) . '.png' : 'default.png' ?>')"></div>
                                    <div class="item-name"><?= escape($item['name']) ?></div>
                                    <div class="item-quantity">x<?= $item['quantity'] ?></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div style="text-align:center;padding:20px;color:#666;">
                                Không có thông tin phần thưởng
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="giftcode-list">
                    <div class="no-giftcodes">
                        😔 Hiện tại không có giftcode nào khả dụng<br>
                        <small>Vui lòng quay lại sau!</small>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/frontend/home/v1/js/jquery.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        function toggleGiftcode(index) {
            const item = document.getElementById('giftcode-' + index);
            item.classList.toggle('expanded');
        }
    </script>
</body>
</html>
