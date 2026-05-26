<!DOCTYPE html>
<!--
  Cookie查看器
  
  @author  Guojin0826
  @email   jinrcsy@gmail.com
  @github  https://github.com/Guojin0826
-->
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>窃取的Cookie数据 - XSS演示</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Microsoft YaHei', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .header h1 {
            color: #e74c3c;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .header p {
            color: #666;
            font-size: 14px;
        }
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .controls {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-refresh {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-refresh:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-clear {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
        }
        .btn-clear:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
        }
        .btn-auto {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        .btn-auto:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(17, 153, 142, 0.4);
        }
        .content {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .cookie-item {
            background: #f8f9fa;
            border-left: 4px solid #e74c3c;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .cookie-item:last-child {
            margin-bottom: 0;
        }
        .cookie-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .cookie-time {
            color: #e74c3c;
            font-weight: bold;
            font-size: 16px;
        }
        .cookie-ip {
            color: #666;
            font-size: 14px;
        }
        .cookie-detail {
            margin-bottom: 10px;
        }
        .cookie-detail-label {
            color: #666;
            font-size: 13px;
            margin-bottom: 5px;
        }
        .cookie-detail-value {
            color: #333;
            font-size: 14px;
            word-break: break-all;
            background: white;
            padding: 10px;
            border-radius: 3px;
            border: 1px solid #dee2e6;
        }
        .cookie-content {
            background: #2d2d2d;
            color: #00ff00;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            white-space: pre-wrap;
            word-break: break-all;
            max-height: 300px;
            overflow-y: auto;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .empty-state-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        .empty-state-text {
            font-size: 16px;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-item {
            flex: 1;
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .stat-value {
            font-size: 36px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        .auto-refresh-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #d4edda;
            color: #155724;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 13px;
            margin-left: 10px;
        }
        .auto-refresh-indicator.hidden {
            display: none;
        }
        .spinner {
            width: 14px;
            height: 14px;
            border: 2px solid #155724;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .btn-back-demo {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            z-index: 9999;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-back-demo:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }
    </style>
</head>
<body>
    <a href="demo.php" class="btn-back-demo">
        <span>🏠</span>
        <span>返回演示首页</span>
    </a>
    <div class="container">
        <div class="header">
            <h1>🍪 窃取的Cookie数据</h1>
            <p>XSS攻击演示 - Cookie窃取监控面板</p>
            <div class="warning-box">
                ⚠️ 本页面仅用于安全教学演示，请勿用于非法用途！
            </div>
        </div>

        <div class="stats">
            <div class="stat-item">
                <div class="stat-value" id="totalCount">0</div>
                <div class="stat-label">总窃取次数</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" id="uniqueIPs">0</div>
                <div class="stat-label">独立IP数</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" id="lastTime">-</div>
                <div class="stat-label">最近窃取时间</div>
            </div>
        </div>

        <div class="controls">
            <button class="btn btn-refresh" onclick="location.reload()">🔄 刷新数据</button>
            <button class="btn btn-auto" id="autoRefreshBtn" onclick="toggleAutoRefresh()">
                ▶️ 开启自动刷新
            </button>
            <button class="btn btn-clear" onclick="clearCookies()">🗑️ 清空所有数据</button>
            <div class="auto-refresh-indicator hidden" id="autoRefreshIndicator">
                <div class="spinner"></div>
                <span>自动刷新中...</span>
            </div>
        </div>

        <div class="content" id="cookieList">
            <?php
            $log_file = 'data/stolen_cookies.txt';
            if(file_exists($log_file)) {
                $content = file_get_contents($log_file);
                $entries = explode("========================================\n", $content);
                $entries = array_filter(array_map('trim', $entries));
                $entries = array_reverse($entries); // 最新的显示在前面
                
                $total_count = count($entries);
                $ips = [];
                $last_time = '-';
                
                if(!empty($entries)) {
                    // 提取第一个条目的时间作为最近时间
                    if(preg_match('/时间: (.+)/', $entries[0], $matches)) {
                        $last_time = $matches[1];
                    }
                    
                    // 统计独立IP
                    foreach($entries as $entry) {
                        if(preg_match('/IP地址: (.+)/', $entry, $matches)) {
                            $ips[] = trim($matches[1]);
                        }
                    }
                    $unique_ips = count(array_unique($ips));
                }
                
                echo "<script>";
                echo "document.getElementById('totalCount').textContent = '$total_count';";
                echo "document.getElementById('uniqueIPs').textContent = '$unique_ips';";
                echo "document.getElementById('lastTime').textContent = '$last_time';";
                echo "</script>";
                
                if(!empty($entries)) {
                    foreach($entries as $entry) {
                        if(empty(trim($entry))) continue;
                        
                        $time = '';
                        $ip = '';
                        $referer = '';
                        $user_agent = '';
                        $cookie = '';
                        
                        if(preg_match('/时间: (.+)/', $entry, $matches)) $time = trim($matches[1]);
                        if(preg_match('/IP地址: (.+)/', $entry, $matches)) $ip = trim($matches[1]);
                        if(preg_match('/来源页面: (.+)/', $entry, $matches)) $referer = trim($matches[1]);
                        if(preg_match('/User-Agent: (.+)/', $entry, $matches)) $user_agent = trim($matches[1]);
                        if(preg_match('/Cookie内容:\n(.+)/s', $entry, $matches)) $cookie = trim($matches[1]);
                        
                        echo '<div class="cookie-item">';
                        echo '<div class="cookie-header">';
                        echo '<span class="cookie-time">📅 ' . htmlspecialchars($time) . '</span>';
                        echo '<span class="cookie-ip">🌐 IP: ' . htmlspecialchars($ip) . '</span>';
                        echo '</div>';
                        
                        echo '<div class="cookie-detail">';
                        echo '<div class="cookie-detail-label">来源页面：</div>';
                        echo '<div class="cookie-detail-value">' . htmlspecialchars($referer) . '</div>';
                        echo '</div>';
                        
                        echo '<div class="cookie-detail">';
                        echo '<div class="cookie-detail-label">User-Agent：</div>';
                        echo '<div class="cookie-detail-value">' . htmlspecialchars($user_agent) . '</div>';
                        echo '</div>';
                        
                        echo '<div class="cookie-detail">';
                        echo '<div class="cookie-detail-label">Cookie内容：</div>';
                        echo '<div class="cookie-content">' . htmlspecialchars($cookie) . '</div>';
                        echo '</div>';
                        
                        echo '</div>';
                    }
                } else {
                    echo '<div class="empty-state">';
                    echo '<div class="empty-state-icon">🍪</div>';
                    echo '<div class="empty-state-text">暂无窃取的Cookie数据</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="empty-state">';
                echo '<div class="empty-state-icon">🍪</div>';
                echo '<div class="empty-state-text">暂无窃取的Cookie数据</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <script>
    let autoRefreshInterval = null;
    
    function toggleAutoRefresh() {
        const btn = document.getElementById('autoRefreshBtn');
        const indicator = document.getElementById('autoRefreshIndicator');
        
        if(autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
            btn.textContent = '▶️ 开启自动刷新';
            indicator.classList.add('hidden');
        } else {
            autoRefreshInterval = setInterval(() => {
                location.reload();
            }, 5000);
            btn.textContent = '⏸️ 停止自动刷新';
            indicator.classList.remove('hidden');
        }
    }
    
    function clearCookies() {
        if(confirm('确定要清空所有窃取的Cookie数据吗？此操作不可恢复！')) {
            window.location.href = 'clear_cookies.php';
        }
    }
    </script>
</body>
</html>