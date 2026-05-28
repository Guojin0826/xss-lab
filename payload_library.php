<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline' https://cdn.bootcdn.net; font-src 'self' https://cdn.bootcdn.net; img-src 'self' data:; connect-src 'self';">
    <title>XSS Payload库 - 安全测试工具</title>
    <link href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .back-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .back-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .stats {
            display: flex;
            justify-content: space-around;
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9em;
        }

        .search-box {
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .search-input {
            width: 100%;
            padding: 15px 20px;
            font-size: 16px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            outline: none;
            transition: border-color 0.3s;
        }

        .search-input:focus {
            border-color: #667eea;
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .filter-btn {
            padding: 8px 16px;
            border: 2px solid #667eea;
            background: white;
            color: #667eea;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }

        .filter-btn:hover, .filter-btn.active {
            background: #667eea;
            color: white;
        }

        .payloads-container {
            padding: 20px;
        }

        .payload-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s;
            border-left: 4px solid #667eea;
        }

        .payload-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .payload-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .payload-name {
            font-size: 1.2em;
            font-weight: bold;
            color: #333;
        }

        .payload-level {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: bold;
        }

        .level-low {
            background: #d4edda;
            color: #155724;
        }

        .level-medium {
            background: #fff3cd;
            color: #856404;
        }

        .level-high {
            background: #f8d7da;
            color: #721c24;
        }

        .level-critical {
            background: #721c24;
            color: white;
        }

        .payload-desc {
            color: #6c757d;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .payload-code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            overflow-x: auto;
            margin-bottom: 15px;
            position: relative;
        }

        .payload-code code {
            white-space: pre-wrap;
            word-break: break-all;
        }

        .copy-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #667eea;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.3s;
        }

        .copy-btn:hover {
            background: #5568d3;
        }

        .payload-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }

        .tag {
            background: #e9ecef;
            color: #495057;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.85em;
        }

        /* 触发说明样式 */
        .trigger-info {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 12px 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .trigger-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .trigger-icon {
            font-size: 1.1em;
        }

        .trigger-text {
            font-weight: 600;
        }

        .trigger-desc {
            color: #6c757d;
            font-size: 0.85em;
            line-height: 1.5;
        }

        .payload-actions {
            display: flex;
            gap: 10px;
        }

        .test-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.3s;
        }

        .test-btn:hover {
            background: #218838;
        }

        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #333;
            color: white;
            padding: 15px 25px;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            transform: translateX(400px);
            transition: transform 0.3s;
            z-index: 1000;
        }

        .toast.show {
            transform: translateX(0);
        }

        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px;
            border-radius: 5px;
        }

        .warning-box h3 {
            color: #856404;
            margin-bottom: 10px;
        }

        .warning-box p {
            color: #856404;
            line-height: 1.6;
        }

        .no-results {
            text-align: center;
            padding: 50px;
            color: #6c757d;
        }

        .no-results i {
            font-size: 4em;
            margin-bottom: 20px;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-bug"></i> XSS Payload库</h1>
            <p>安全测试专用工具 - 请勿用于非法用途</p>
        </div>
        
        <a href="demo.php" class="back-btn">🏠 返回主页</a>

        <div class="stats">
            <div class="stat-item">
                <div class="stat-number" id="totalCount">0</div>
                <div class="stat-label">总计Payload</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="lowCount">0</div>
                <div class="stat-label">低危</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="mediumCount">0</div>
                <div class="stat-label">中危</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="highCount">0</div>
                <div class="stat-label">高危</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="criticalCount">0</div>
                <div class="stat-label">严重</div>
            </div>
        </div>

        <div class="warning-box">
            <h3><i class="fas fa-exclamation-triangle"></i> 安全警告</h3>
            <p>本页面仅用于安全研究和教学目的。所有Payload仅供测试使用，请勿用于非法攻击。使用本工具进行非法活动的一切后果由使用者自行承担。</p>
        </div>

        <div class="search-box">
            <input type="text" class="search-input" id="searchInput" placeholder="搜索Payload名称、描述或标签...">
        </div>

        <div class="filters" id="filters">
            <button class="filter-btn active" data-filter="all">全部</button>
            <button class="filter-btn" data-filter="JavaScript URL">JavaScript URL</button>
            <button class="filter-btn" data-filter="事件处理器">事件处理器</button>
            <button class="filter-btn" data-filter="Script标签">Script标签</button>
            <button class="filter-btn" data-filter="HTML注入">HTML注入</button>
            <button class="filter-btn" data-filter="CSS注入">CSS注入</button>
            <button class="filter-btn" data-filter="特殊绕过">特殊绕过</button>
        </div>

        <div class="payloads-container" id="payloadsContainer">
            <!-- Payloads will be loaded here -->
        </div>
    </div>

    <div class="toast" id="toast">
        <span id="toastMessage"></span>
    </div>

    <script src="payload_library.js"></script>
</body>
</html>