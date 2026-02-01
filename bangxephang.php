<?php
require_once 'includes/functions.php';

global $pdo;

// Hàm parse sức mạnh từ datapoint (copy từ profile.php)
function getPowerFromDatapoint($datapoint) {
    if (empty($datapoint)) {
        return 0;
    }
    
    $data = json_decode($datapoint, true);
    if (is_array($data) && count($data) > 1) {
        return (int)$data[1];
    }
    
    return 0;
}

// Hàm lấy ID nhiệm vụ từ data_task
function getTaskIdFromDataTask($dataTask) {
    if (empty($dataTask)) {
        return 0;
    }
    
    $data = json_decode($dataTask, true);
    if (is_array($data) && count($data) > 0) {
        return (int)$data[0];
    }
    
    return 0;
}

// Hàm lấy tên nhiệm vụ từ data_task
function getTaskNameFromDataTask($dataTask) {
    global $pdo;
    
    if (empty($dataTask)) {
        return 'Chưa có nhiệm vụ';
    }
    
    $data = json_decode($dataTask, true);
    if (is_array($data) && count($data) > 0) {
        $taskId = (int)$data[0];
        
        try {
            $stmt = $pdo->prepare("SELECT name FROM task_main_template WHERE id = ? LIMIT 1");
            $stmt->execute([$taskId]);
            $task = $stmt->fetch();
            
            if ($task && !empty($task['name'])) {
                return $task['name'];
            }
        } catch (Exception $e) {
            // Fallback if task_main_template doesn't exist or query fails
        }
    }
    
    return 'Nhiệm vụ #' . (isset($taskId) ? $taskId : '0');
}

// Lấy TOP 10 nạp tiền (chỉ lấy nhân vật)
$stmt = $pdo->prepare("
    SELECT p.name as player_name, COALESCE(a.danap, 0) as danap 
    FROM account a 
    JOIN player p ON a.id = p.account_id
    WHERE a.active = 1 
    ORDER BY a.danap DESC, a.cash DESC 
    LIMIT 10
");
$stmt->execute();
$topNap = $stmt->fetchAll();

// Lấy TOP 10 sức mạnh (chỉ lấy nhân vật)
$stmt = $pdo->prepare("
    SELECT p.name as player_name, p.data_point
    FROM account a 
    JOIN player p ON a.id = p.account_id 
    WHERE a.active = 1 AND p.data_point IS NOT NULL AND p.data_point != ''
    ORDER BY CAST(JSON_EXTRACT(p.data_point, '$[1]') AS UNSIGNED) DESC 
    LIMIT 10
");
$stmt->execute();
$topPower = $stmt->fetchAll();

// Lấy TOP 10 nhiệm vụ (chỉ lấy nhân vật)
$stmt = $pdo->prepare("
    SELECT p.name as player_name, p.data_task
    FROM account a 
    JOIN player p ON a.id = p.account_id 
    WHERE a.active = 1 AND p.data_task IS NOT NULL AND p.data_task != ''
    ORDER BY CAST(JSON_EXTRACT(p.data_task, '$[0]') AS UNSIGNED) DESC 
    LIMIT 10
");
$stmt->execute();
$topTask = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'includes/header.php'; ?>
    <link rel="stylesheet" href="/assets/frontend/home/v1/css/stylea6ca.css?v=919" />
    <link rel="stylesheet" href="/assets/css/post.css" />
    <title>Bảng Xếp Hạng - <?= get_setting('site_name') ?></title>
    <style>
        .rankings-container {
            max-width: 1200px;
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
        
        .tabs-container {
            background: #fff;
            border: 3px solid #222;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .tab-navigation {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            padding: 20px;
            background: #fff;
            border-bottom: 3px solid #222;
        }
        
        .tab-btn {
            padding: 15px 20px;
            font-family: 'Bangers', cursive;
            font-size: 1.4em;
            color: #fff;
            background: linear-gradient(45deg, #ff6b35, #ff8c42);
            border: 3px solid #222;
            border-radius: 12px;
            cursor: pointer;
            text-shadow: 2px 2px 0 #222;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .tab-btn.active {
            background: linear-gradient(45deg, #28a745, #32d74b);
            color: #fff;
            text-shadow: 2px 2px 0 #222;
            transform: translateY(-2px);
        }
        
        .tab-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        }
        
        .tab-btn.active:hover {
            transform: translateY(-2px);
        }
        
        .tab-content {
            display: none;
            padding: 20px;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .ranking-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Bangers', cursive;
        }
        
        .ranking-table th {
            background: linear-gradient(45deg, #ff6b35, #ff8c42);
            color: #fff;
            padding: 12px 8px;
            text-align: center;
            font-size: 1.3em;
            text-shadow: 1px 1px 0 #222;
            border: 2px solid #222;
            letter-spacing: 1px;
        }
        
        .ranking-table td {
            padding: 12px 8px;
            text-align: center;
            border: 2px solid #222;
            font-size: 1.1em;
            background: #f8f9fa;
        }
        
        /* Override global CSS that adds dark background to first table row */
        .ranking-table tbody tr:first-child td {
            background: #f8f9fa !important;
            color: #222 !important;
        }
        
        /* Ensure consistent text colors for all ranking values */
        .ranking-table tbody tr:first-child .money-value {
            color: #28a745 !important;
        }
        
        .ranking-table tbody tr:first-child .power-value {
            color: #ff6b35 !important;
        }
        
        .ranking-table tbody tr:first-child .task-value {
            color: #007bff !important;
        }
        
        .ranking-table tbody tr:first-child .player-name {
            color: #1a202c !important;
        }
        
        .rank-number {
            font-weight: bold;
            font-size: 1.2em;
            color: #222;
            text-shadow: 1px 1px 0 #fff;
            white-space: nowrap;
            min-width: 80px;
        }
        
        .player-name {
            font-weight: bold;
            color: #1a202c;
        }
        
        .power-value {
            color: #ff6b35;
            font-weight: bold;
        }
        
        .money-value {
            color: #28a745;
            font-weight: bold;
        }
        
        .task-value {
            color: #007bff;
            font-weight: bold;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            font-family: 'Bangers', cursive;
            font-size: 1.5em;
            color: #666;
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            .rankings-container {
                margin: 15px auto;
                padding: 0 10px;
            }
            
            .main-title {
                font-size: 2em;
                padding: 15px;
                margin-bottom: 20px;
            }
            
            .tab-navigation {
                grid-template-columns: 1fr 1fr 1fr !important;
                gap: 8px !important;
                padding: 15px !important;
            }
            
            .tab-btn {
                font-size: 1em !important;
                padding: 10px 8px !important;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            .tab-content {
                padding: 15px;
            }
            
            .ranking-table th,
            .ranking-table td {
                padding: 8px 4px;
                font-size: 0.9em;
            }
            
            .ranking-table th {
                font-size: 1.1em;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <div class="breadcrumb">
                <a href="/">Trang chủ</a> > <span>Bảng xếp hạng</span>
            </div>
            
            <div class="rankings-container">
                <!-- Navigation Tabs -->
                <div class="tab-navigation">
                    <button class="tab-btn active" onclick="showTab('nap')">NẠP TIỀN</button>
                    <button class="tab-btn" onclick="showTab('power')">SỨC MẠNH</button>
                    <button class="tab-btn" onclick="showTab('task')">NHIỆM VỤ</button>
                </div>

                <!-- Tab Content: TOP NẠP TIỀN -->
                <div id="tab-nap" class="tab-content active">
                    <?php if (!empty($topNap)): ?>
                    <table class="ranking-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Hạng</th>
                                <th>Nhân vật</th>
                                <th>Tổng nạp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topNap as $index => $user): ?>
                            <tr>
                                <td class="rank-number">
                                    <?php if ($index == 0): ?>
                                        🥇 #1
                                    <?php elseif ($index == 1): ?>
                                        🥈 #2
                                    <?php elseif ($index == 2): ?>
                                        🥉 #3
                                    <?php else: ?>
                                        #<?= $index + 1 ?>
                                    <?php endif; ?>
                                </td>
                                <td class="player-name"><?= escape($user['player_name']) ?></td>
                                <td class="money-value"><?= number_format($user['danap']) ?> VND</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="no-data">Chưa có dữ liệu xếp hạng nạp tiền</div>
                    <?php endif; ?>
                </div>

                <!-- Tab Content: TOP SỨC MẠNH -->
                <div id="tab-power" class="tab-content">
                    <?php if (!empty($topPower)): ?>
                    <table class="ranking-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Hạng</th>
                                <th>Nhân vật</th>
                                <th>Sức mạnh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topPower as $index => $player): ?>
                            <tr>
                                <td class="rank-number">
                                    <?php if ($index == 0): ?>
                                        🥇 #1
                                    <?php elseif ($index == 1): ?>
                                        🥈 #2
                                    <?php elseif ($index == 2): ?>
                                        🥉 #3
                                    <?php else: ?>
                                        #<?= $index + 1 ?>
                                    <?php endif; ?>
                                </td>
                                <td class="player-name"><?= escape($player['player_name']) ?></td>
                                <td class="power-value"><?= number_format(getPowerFromDatapoint($player['data_point'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="no-data">Chưa có dữ liệu xếp hạng sức mạnh</div>
                    <?php endif; ?>
                </div>

                <!-- Tab Content: TOP NHIỆM VỤ -->
                <div id="tab-task" class="tab-content">
                    <?php if (!empty($topTask)): ?>
                    <table class="ranking-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Hạng</th>
                                <th>Nhân vật</th>
                                <th>Nhiệm vụ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topTask as $index => $player): ?>
                            <tr>
                                <td class="rank-number">
                                    <?php if ($index == 0): ?>
                                        🥇 #1
                                    <?php elseif ($index == 1): ?>
                                        🥈 #2
                                    <?php elseif ($index == 2): ?>
                                        🥉 #3
                                    <?php else: ?>
                                        #<?= $index + 1 ?>
                                    <?php endif; ?>
                                </td>
                                <td class="player-name"><?= escape($player['player_name']) ?></td>
                                <td class="task-value"><?= getTaskNameFromDataTask($player['data_task']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="no-data">Chưa có dữ liệu xếp hạng nhiệm vụ</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="/assets/frontend/home/v1/js/jquery.min.js"></script>
    <script src="/assets/js/main.js"></script>
    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById('tab-' + tabName).classList.add('active');
            
            // Add active class to clicked button
            event.target.classList.add('active');
        }
        
        // Handle URL anchors for direct navigation
        document.addEventListener('DOMContentLoaded', function() {
            const hash = window.location.hash.substr(1);
            if (hash && ['nap', 'power', 'task'].includes(hash)) {
                // Hide all tabs
                document.querySelectorAll('.tab-content').forEach(tab => {
                    tab.classList.remove('active');
                });
                document.querySelectorAll('.tab-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Show targeted tab
                document.getElementById('tab-' + hash).classList.add('active');
                document.querySelector('[onclick="showTab(\'' + hash + '\')"]').classList.add('active');
            }
        });
    </script>
</body>
</html>
