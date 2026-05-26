<!DOCTYPE html>
<!--
  凭据查看器
  
  @author  Guojin0826
  @email   jinrcsy@gmail.com
  @github  https://github.com/Guojin0826
-->
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>凭据查看器 - XSS演示</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Microsoft YaHei', Arial, sans-serif;
            background: #1a1a2e;
            color: #fff;
            padding: 20px;
        }
        .container { max-width: 1000px; margin: 0 auto; }
        h1 { color: #e94560; margin-bottom: 20px; }
        .warning {
            background: linear-gradient(90deg, #e94560, #ff6b6b);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-box {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 8px;
            flex: 1;
            text-align: center;
        }
        .stat-number { font-size: 36px; color: #4ecdc4; font-weight: bold; }
        .stat-label { color: #aaa; margin-top: 5px; }
        .credentials-list {
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 20px;
        }
        .credential-item {
            background: rgba(0,0,0,0.3);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #e94560;
        }
        .credential-item:last-child { margin-bottom: 0; }
        .credential-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: #4ecdc4;
            font-weight: bold;
        }
        .credential-detail {
            display: grid;
            grid-template-columns: 100px 1fr;
            gap: 8px;
            font-size: 14px;
        }
        .credential-label { color: #aaa; }
        .credential-value { color: #fff; word-break: break-all; }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #aaa;
        }
        .btn-clear {
            background: #e94560;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        .btn-clear:hover { background: #d63850; }
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
        <h1>🔐 窃取的凭据查看器</h1>
        <div class="warning">
            ⚠️ 此页面仅用于安全演示目的！展示XSS攻击的危害性。
        </div>
        
        <div class="stats">
            <div class="stat-box">
                <div class="stat-number" id="totalCount">0</div>
                <div class="stat-label">窃取凭据总数</div>
            </div>
            <div class="stat-box">
                <div class="stat-number" id="todayCount">0</div>
                <div class="stat-label">今日窃取</div>
            </div>
        </div>
        
        <div class="credentials-list">
            <h2 style="margin-bottom: 15px; color: #4ecdc4;">📋 凭据列表</h2>
            <div id="credentialsContainer">
                <div class="empty-state">暂无窃取的凭据</div>
            </div>
            <button class="btn-clear" onclick="clearCredentials()">清空所有数据</button>
        </div>
    </div>

    <script>
        function loadCredentials() {
            fetch('data/stolen_credentials.txt')
                .then(response => response.json())
                .then(data => {
                    displayCredentials(data);
                })
                .catch(error => {
                    document.getElementById('credentialsContainer').innerHTML = 
                        '<div class="empty-state">暂无窃取的凭据</div>';
                });
        }
        
        function displayCredentials(data) {
            if (!data || data.length === 0) {
                document.getElementById('credentialsContainer').innerHTML = 
                    '<div class="empty-state">暂无窃取的凭据</div>';
                document.getElementById('totalCount').textContent = '0';
                document.getElementById('todayCount').textContent = '0';
                return;
            }
            
            document.getElementById('totalCount').textContent = data.length;
            
            // 统计今日数量
            var today = new Date().toLocaleDateString();
            var todayCount = data.filter(item => {
                if (item.time) {
                    return item.time.includes(today.replace(/\//g, '-'));
                }
                return false;
            }).length;
            document.getElementById('todayCount').textContent = todayCount;
            
            // 显示凭据列表
            var html = '';
            data.reverse().forEach((item, index) => {
                html += `
                    <div class="credential-item">
                        <div class="credential-header">
                            <span>凭据 #${data.length - index}</span>
                            <span>${item.time || item.timestamp || '未知时间'}</span>
                        </div>
                        <div class="credential-detail">
                            <div class="credential-label">用户名：</div>
                            <div class="credential-value">${item.username || '未知'}</div>
                            <div class="credential-label">密码：</div>
                            <div class="credential-value" style="color: #e94560; font-weight: bold;">${item.password || '未知'}</div>
                            <div class="credential-label">IP地址：</div>
                            <div class="credential-value">${item.ip || '未知'}</div>
                            <div class="credential-label">来源页面：</div>
                            <div class="credential-value">${item.referer || '直接访问'}</div>
                        </div>
                    </div>
                `;
            });
            
            document.getElementById('credentialsContainer').innerHTML = html;
        }
        
        function clearCredentials() {
            if (confirm('确定要清空所有窃取的凭据吗？')) {
                fetch('clear_credentials.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            loadCredentials();
                        }
                    });
            }
        }
        
        // 页面加载时获取凭据
        loadCredentials();
        
        // 每5秒自动刷新
        setInterval(loadCredentials, 5000);
    </script>
</body>
</html>