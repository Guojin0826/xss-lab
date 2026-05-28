<?php
/**
 * XSS类型演示模块 - 入门学习
 * 包含：反射型XSS、存储型XSS、DOM型XSS
 */

// 存储型XSS数据文件
$comments_file = __DIR__ . '/data/xss_demo_comments.txt';
if (!file_exists(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0777, true);
}

// 处理评论提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = $_POST['comment'];
    $username = isset($_POST['username']) ? $_POST['username'] : '匿名用户';
    $time = date('Y-m-d H:i:s');
    $entry = json_encode(['username' => $username, 'comment' => $comment, 'time' => $time]) . "\n";
    file_put_contents($comments_file, $entry, FILE_APPEND);
    header('Location: ?section=stored#stored');
    exit;
}

// 读取评论
$comments = [];
if (file_exists($comments_file)) {
    $lines = file($comments_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $data = json_decode($line, true);
        if ($data) {
            $comments[] = $data;
        }
    }
}

// 获取当前section
$section = isset($_GET['section']) ? $_GET['section'] : 'reflected';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XSS类型演示 - 入门学习</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', 'Microsoft YaHei', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            color: #333;
        }
        
        /* 返回按钮 */
        .back-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
            z-index: 1000;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .back-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        
        /* 页面标题 */
        .page-header {
            text-align: center;
            margin-bottom: 40px;
            padding-top: 20px;
        }
        
        .page-header h1 {
            color: #fff;
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .page-header .subtitle {
            color: #a0a0a0;
            font-size: 1.1em;
        }
        
        .page-header .badge {
            display: inline-block;
            background: linear-gradient(135deg, #00b894, #00cec9);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85em;
            margin-top: 10px;
        }
        
        /* Tab导航 */
        .tab-nav {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .tab-btn {
            padding: 15px 30px;
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.2);
            color: #fff;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .tab-btn:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }
        
        .tab-btn.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-color: transparent;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .tab-btn .icon {
            margin-right: 8px;
        }
        
        /* 内容区域 */
        .content-section {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            display: none;
        }
        
        .content-section.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* 类型标题 */
        .type-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }
        
        .type-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }
        
        .type-icon.reflected { background: linear-gradient(135deg, #e74c3c, #c0392b); }
        .type-icon.stored { background: linear-gradient(135deg, #f39c12, #e67e22); }
        .type-icon.dom { background: linear-gradient(135deg, #9b59b6, #8e44ad); }
        
        .type-title h2 {
            color: #2c3e50;
            font-size: 1.8em;
        }
        
        .type-title .en-name {
            color: #7f8c8d;
            font-size: 0.9em;
        }
        
        /* 原理说明 */
        .principle-box {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            border-left: 5px solid #667eea;
        }
        
        .principle-box h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .principle-box p {
            color: #555;
            line-height: 1.8;
            margin-bottom: 10px;
        }
        
        .principle-box .highlight {
            background: #fff3cd;
            padding: 2px 8px;
            border-radius: 4px;
            color: #856404;
            font-weight: 600;
        }
        
        /* 流程图 */
        .flow-diagram {
            background: #1a1a2e;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            overflow-x: auto;
        }
        
        .flow-diagram h4 {
            color: #fff;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .flow-steps {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .flow-step {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 0.9em;
            text-align: center;
            min-width: 120px;
        }
        
        .flow-step.attack {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }
        
        .flow-step.victim {
            background: linear-gradient(135deg, #f39c12, #e67e22);
        }
        
        .flow-step.server {
            background: linear-gradient(135deg, #00b894, #00cec9);
        }
        
        .flow-arrow {
            color: #667eea;
            font-size: 1.5em;
        }
        
        /* 演示区域 */
        .demo-area {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            border: 2px dashed #dee2e6;
        }
        
        .demo-area h4 {
            color: #2c3e50;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .demo-area h4::before {
            content: '🎮';
            font-size: 1.2em;
        }
        
        /* 搜索框样式 */
        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .search-input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1em;
            transition: border-color 0.3s;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .search-btn {
            padding: 15px 30px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        /* 搜索结果 */
        .search-result {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #e0e0e0;
        }
        
        .search-result h5 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .search-result .result-text {
            color: #555;
            line-height: 1.6;
        }
        
        /* 评论表单 */
        .comment-form {
            margin-bottom: 25px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1em;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #00b894, #00cec9);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 184, 148, 0.4);
        }
        
        /* 评论列表 */
        .comments-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .comment-item {
            background: #fff;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #e0e0e0;
            transition: box-shadow 0.3s;
        }
        
        .comment-item:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .comment-author {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .comment-time {
            color: #999;
            font-size: 0.85em;
        }
        
        .comment-content {
            color: #555;
            line-height: 1.6;
        }
        
        /* DOM XSS演示 */
        .dom-demo {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        @media (max-width: 768px) {
            .dom-demo {
                grid-template-columns: 1fr;
            }
        }
        
        .dom-demo-item {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #e0e0e0;
        }
        
        .dom-demo-item h5 {
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        
        .dom-demo-item input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        
        .dom-demo-item button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.3s;
        }
        
        .dom-demo-item button:hover {
            transform: translateY(-2px);
        }
        
        .dom-output {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            min-height: 50px;
            border: 1px dashed #dee2e6;
        }
        
        /* Payload提示 */
        .payload-hints {
            background: #fff3cd;
            border-radius: 10px;
            padding: 20px;
            margin-top: 25px;
            border: 1px solid #ffc107;
        }
        
        .payload-hints h4 {
            color: #856404;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .payload-hints ul {
            list-style: none;
            padding: 0;
        }
        
        .payload-hints li {
            background: #fff;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            color: #333;
            cursor: pointer;
            transition: background 0.3s;
            border: 1px solid #e0e0e0;
        }
        
        .payload-hints li:hover {
            background: #e8f4fd;
            border-color: #2196f3;
        }
        
        .payload-hints li::before {
            content: '💡 ';
        }
        
        /* 防御建议 */
        .defense-box {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border-radius: 15px;
            padding: 25px;
            margin-top: 25px;
            border-left: 5px solid #28a745;
        }
        
        .defense-box h4 {
            color: #155724;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .defense-box ul {
            color: #155724;
            padding-left: 20px;
        }
        
        .defense-box li {
            margin-bottom: 8px;
            line-height: 1.6;
        }
        
        .defense-box code {
            background: #fff;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        
        /* 危险等级 */
        .danger-level {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }
        
        .danger-level.high {
            background: #f8d7da;
            color: #721c24;
        }
        
        .danger-level.medium {
            background: #fff3cd;
            color: #856404;
        }
        
        .danger-level.low {
            background: #d4edda;
            color: #155724;
        }
        
        /* 清空按钮 */
        .clear-btn {
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background 0.3s;
        }
        
        .clear-btn:hover {
            background: #c0392b;
        }
        
        /* Toast提示 */
        .toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: #333;
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 2000;
        }
        
        .toast.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
        
        .toast.success {
            background: linear-gradient(135deg, #00b894, #00cec9);
        }
        
        .toast.error {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }
    </style>
</head>
<body>
    <a href="demo.php" class="back-btn">🏠 返回演示首页</a>
    
    <div class="container">
        <div class="page-header">
            <h1>🛡️ XSS漏洞类型演示</h1>
            <p class="subtitle">了解三种主要XSS攻击类型的原理与危害</p>
            <span class="badge">📚 入门学习模块</span>
        </div>
        
        <!-- Tab导航 -->
        <div class="tab-nav">
            <a href="?section=reflected#reflected" class="tab-btn <?php echo $section === 'reflected' ? 'active' : ''; ?>">
                <span class="icon">⚡</span>反射型XSS
            </a>
            <a href="?section=stored#stored" class="tab-btn <?php echo $section === 'stored' ? 'active' : ''; ?>">
                <span class="icon">💾</span>存储型XSS
            </a>
            <a href="?section=dom#dom" class="tab-btn <?php echo $section === 'dom' ? 'active' : ''; ?>">
                <span class="icon">🌐</span>DOM型XSS
            </a>
        </div>
        
        <!-- 反射型XSS -->
        <div id="reflected" class="content-section <?php echo $section === 'reflected' ? 'active' : ''; ?>">
            <div class="type-header">
                <div class="type-icon reflected">⚡</div>
                <div class="type-title">
                    <h2>反射型XSS (Reflected XSS)</h2>
                    <span class="en-name">Non-persistent XSS</span>
                </div>
                <span class="danger-level medium">⚠️ 中等危害</span>
            </div>
            
            <div class="principle-box">
                <h3>📖 原理说明</h3>
                <p>反射型XSS是最常见的XSS漏洞类型。攻击者将恶意脚本嵌入到URL参数中，服务器接收参数后<span class="highlight">未经过滤直接输出</span>到响应页面，导致脚本在受害者浏览器中执行。</p>
                <p>攻击者通常通过诱导用户点击恶意链接来实施攻击，例如发送包含恶意链接的邮件、在论坛发布恶意链接等。</p>
            </div>
            
            <div class="flow-diagram">
                <h4>🔄 攻击流程</h4>
                <div class="flow-steps">
                    <div class="flow-step attack">攻击者构造恶意URL</div>
                    <span class="flow-arrow">→</span>
                    <div class="flow-step victim">诱导受害者点击</div>
                    <span class="flow-arrow">→</span>
                    <div class="flow-step server">服务器反射恶意代码</div>
                    <span class="flow-arrow">→</span>
                    <div class="flow-step victim">受害者浏览器执行</div>
                </div>
            </div>
            
            <div class="demo-area">
                <h4>漏洞演示 - 搜索功能</h4>
                <form class="search-form" method="GET" action="">
                    <input type="text" name="search" class="search-input" placeholder="输入搜索关键词..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <input type="hidden" name="section" value="reflected">
                    <button type="submit" class="search-btn">🔍 搜索</button>
                </form>
                
                <?php if (isset($_GET['search']) && $_GET['search'] !== ''): ?>
                <div class="search-result">
                    <h5>搜索结果：</h5>
                    <p class="result-text">您搜索的关键词是：<strong><?php echo $_GET['search']; // 故意存在XSS漏洞 ?></strong></p>
                    <p style="color: #999; font-size: 0.9em; margin-top: 10px;">找到约 <?php echo rand(1, 100); ?> 条相关结果</p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="payload-hints">
                <h4>🎯 测试Payload（点击复制）</h4>
                <ul>
                    <li onclick="copyPayload(this)">&lt;script&gt;alert('反射型XSS')&lt;/script&gt;</li>
                    <li onclick="copyPayload(this)">&lt;img src=x onerror="alert('XSS')"&gt;</li>
                    <li onclick="copyPayload(this)">&lt;svg onload="alert('XSS')"&gt;</li>
                    <li onclick="copyPayload(this)">&lt;input onfocus="alert('XSS')" autofocus&gt;</li>
                </ul>
            </div>
            
            <div class="defense-box">
                <h4>🛡️ 防御方法</h4>
                <ul>
                    <li>对所有输出进行 <code>HTML实体编码</code>（如 htmlspecialchars()）</li>
                    <li>设置 <code>Content-Type</code> 响应头，防止MIME类型嗅探</li>
                    <li>启用 <code>Content-Security-Policy (CSP)</code> 策略</li>
                    <li>对URL参数进行严格的输入验证和白名单过滤</li>
                </ul>
            </div>
        </div>
        
        <!-- 存储型XSS -->
        <div id="stored" class="content-section <?php echo $section === 'stored' ? 'active' : ''; ?>">
            <div class="type-header">
                <div class="type-icon stored">💾</div>
                <div class="type-title">
                    <h2>存储型XSS (Stored XSS)</h2>
                    <span class="en-name">Persistent XSS</span>
                </div>
                <span class="danger-level high">🔴 高危害</span>
            </div>
            
            <div class="principle-box">
                <h3>📖 原理说明</h3>
                <p>存储型XSS是最危险的XSS漏洞类型。攻击者将恶意脚本<span class="highlight">永久存储</span>在目标服务器上（如数据库、文件系统），当其他用户浏览相关页面时，恶意脚本会被自动执行。</p>
                <p>常见场景：论坛帖子、评论区、用户资料、留言板等。一次攻击可以影响所有访问该页面的用户。</p>
            </div>
            
            <div class="flow-diagram">
                <h4>🔄 攻击流程</h4>
                <div class="flow-steps">
                    <div class="flow-step attack">攻击者提交恶意内容</div>
                    <span class="flow-arrow">→</span>
                    <div class="flow-step server">服务器存储恶意代码</div>
                    <span class="flow-arrow">→</span>
                    <div class="flow-step victim">其他用户访问页面</div>
                    <span class="flow-arrow">→</span>
                    <div class="flow-step victim">浏览器执行恶意代码</div>
                </div>
            </div>
            
            <div class="demo-area">
                <h4>漏洞演示 - 留言板功能</h4>
                
                <form class="comment-form" method="POST" action="">
                    <div class="form-group">
                        <label>👤 用户名</label>
                        <input type="text" name="username" placeholder="请输入用户名" required>
                    </div>
                    <div class="form-group">
                        <label>💬 留言内容</label>
                        <textarea name="comment" placeholder="请输入留言内容..." required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">📝 提交留言</button>
                </form>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0;">
                    <h5 style="color: #2c3e50;">📋 留言列表 (<?php echo count($comments); ?>条)</h5>
                    <button class="clear-btn" onclick="clearComments()">🗑️ 清空留言</button>
                </div>
                
                <div class="comments-list">
                    <?php if (empty($comments)): ?>
                    <div style="text-align: center; padding: 40px; color: #999;">
                        <p style="font-size: 3em; margin-bottom: 10px;">📭</p>
                        <p>暂无留言，快来发表第一条留言吧！</p>
                    </div>
                    <?php else: ?>
                        <?php foreach (array_reverse($comments) as $comment): ?>
                        <div class="comment-item">
                            <div class="comment-header">
                                <span class="comment-author"><?php echo htmlspecialchars($comment['username']); ?></span>
                                <span class="comment-time"><?php echo $comment['time']; ?></span>
                            </div>
                            <div class="comment-content">
                                <?php echo $comment['comment']; // 故意存在XSS漏洞 ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="payload-hints">
                <h4>🎯 测试Payload（点击复制）</h4>
                <ul>
                    <li onclick="copyPayload(this)">&lt;script&gt;alert('存储型XSS')&lt;/script&gt;</li>
                    <li onclick="copyPayload(this)">&lt;img src=x onerror="alert('您的Cookie: '+document.cookie)"&gt;</li>
                    <li onclick="copyPayload(this)">&lt;svg/onload=alert('XSS')&gt;</li>
                    <li onclick="copyPayload(this)">&lt;body onload="alert('XSS')"&gt;</li>
                    <li onclick="copyPayload(this)">&lt;a href="javascript:alert('XSS')"&gt;点击这里&lt;/a&gt;</li>
                </ul>
            </div>
            
            <div class="defense-box">
                <h4>🛡️ 防御方法</h4>
                <ul>
                    <li><strong>输入过滤</strong>：对用户输入进行严格验证和白名单过滤</li>
                    <li><strong>输出编码</strong>：使用 <code>htmlspecialchars()</code> 等函数进行HTML实体编码</li>
                    <li><strong>内容安全策略</strong>：配置严格的 <code>CSP</code> 策略</li>
                    <li><strong>HttpOnly Cookie</strong>：设置Cookie的HttpOnly属性，防止脚本读取</li>
                </ul>
            </div>
        </div>
        
        <!-- DOM型XSS -->
        <div id="dom" class="content-section <?php echo $section === 'dom' ? 'active' : ''; ?>">
            <div class="type-header">
                <div class="type-icon dom">🌐</div>
                <div class="type-title">
                    <h2>DOM型XSS (DOM-based XSS)</h2>
                    <span class="en-name">Client-side XSS</span>
                </div>
                <span class="danger-level medium">⚠️ 中等危害</span>
            </div>
            
            <div class="principle-box">
                <h3>📖 原理说明</h3>
                <p>DOM型XSS是一种特殊的XSS漏洞，攻击代码<span class="highlight">完全在客户端执行</span>，不经过服务器。攻击者通过修改页面DOM环境，将恶意脚本注入到页面中执行。</p>
                <p>常见触发点：URL参数、location.hash、document.referrer、localStorage等。由于不经过服务器，传统的服务器端防护措施往往无效。</p>
            </div>
            
            <div class="flow-diagram">
                <h4>🔄 攻击流程</h4>
                <div class="flow-steps">
                    <div class="flow-step attack">攻击者构造恶意URL</div>
                    <span class="flow-arrow">→</span>
                    <div class="flow-step victim">受害者访问链接</div>
                    <span class="flow-arrow">→</span>
                    <div class="flow-step victim">浏览器解析URL</div>
                    <span class="flow-arrow">→</span>
                    <div class="flow-step victim">JS动态写入DOM执行</div>
                </div>
            </div>
            
            <div class="demo-area">
                <h4>漏洞演示 - 多种触发方式</h4>
                
                <div class="dom-demo">
                    <!-- innerHTML方式 -->
                    <div class="dom-demo-item">
                        <h5>1️⃣ innerHTML方式</h5>
                        <p style="font-size: 0.85em; color: #666; margin-bottom: 8px;">通过innerHTML将用户输入直接写入DOM，HTML标签会被解析执行</p>
                        <input type="text" id="domInput1" placeholder="输入内容测试innerHTML">
                        <button onclick="testInnerHTML()">测试</button>
                        <div class="dom-output" id="output1">输出区域</div>
                        <div style="margin-top: 8px; font-size: 0.8em;">
                            <strong>测试Payload：</strong><code style="cursor: pointer; background: #e8f5e9; padding: 2px 6px; border-radius: 3px;" onclick="copyText(this)">&lt;img src=x onerror="alert('innerHTML XSS')"&gt;</code>
                        </div>
                    </div>
                    
                    <!-- document.write方式 -->
                    <div class="dom-demo-item">
                        <h5>2️⃣ document.write方式</h5>
                        <p style="font-size: 0.85em; color: #666; margin-bottom: 8px;">通过document.write直接向文档写入HTML，页面加载时执行最危险</p>
                        <input type="text" id="domInput2" placeholder="输入内容测试document.write">
                        <button onclick="testDocWrite()">测试</button>
                        <div class="dom-output" id="output2">输出区域</div>
                        <div style="margin-top: 8px; font-size: 0.8em;">
                            <strong>测试Payload：</strong><code style="cursor: pointer; background: #e8f5e9; padding: 2px 6px; border-radius: 3px;" onclick="copyText(this)">&lt;svg onload="alert('docWrite XSS')"&gt;</code>
                        </div>
                    </div>
                    
                    <!-- eval方式 -->
                    <div class="dom-demo-item">
                        <h5>3️⃣ eval方式</h5>
                        <p style="font-size: 0.85em; color: #666; margin-bottom: 8px;">通过eval()执行用户输入的JavaScript代码，最直接的代码注入</p>
                        <input type="text" id="domInput3" placeholder="输入JS代码测试eval">
                        <button onclick="testEval()">测试</button>
                        <div class="dom-output" id="output3">输出区域</div>
                        <div style="margin-top: 8px; font-size: 0.8em;">
                            <strong>测试Payload：</strong><code style="cursor: pointer; background: #fff3e0; padding: 2px 6px; border-radius: 3px;" onclick="copyText(this)">alert('eval XSS')</code>
                            <span style="color: #e65100; font-size: 0.9em;">⚠️ 注意：这里输入纯JS代码，不需要HTML标签</span>
                        </div>
                    </div>
                    
                    <!-- URL Hash方式 -->
                    <div class="dom-demo-item">
                        <h5>4️⃣ URL Hash方式</h5>
                        <p style="font-size: 0.85em; color: #666; margin-bottom: 8px;">从URL的#后面读取数据并写入DOM，攻击者可构造恶意链接传播</p>
                        <button onclick="testHashXSS()">触发Hash XSS演示</button>
                        <div class="dom-output" id="output4">输出区域</div>
                        <div style="margin-top: 8px; font-size: 0.8em;">
                            <strong>攻击链接示例：</strong><code style="cursor: pointer; background: #e8f5e9; padding: 2px 6px; border-radius: 3px; font-size: 0.9em;" onclick="copyText(this)">#&lt;img src=x onerror="alert('Hash XSS')"&gt;</code>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="payload-hints">
                <h4>🎯 四种触发方式对比说明</h4>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85em;">
                    <thead>
                        <tr style="background: #e3f2fd;">
                            <th style="padding: 8px; border: 1px solid #bbdefb; text-align: left;">触发方式</th>
                            <th style="padding: 8px; border: 1px solid #bbdefb; text-align: left;">原理</th>
                            <th style="padding: 8px; border: 1px solid #bbdefb; text-align: left;">Payload类型</th>
                            <th style="padding: 8px; border: 1px solid #bbdefb; text-align: left;">危险程度</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #e0e0e0;"><strong>innerHTML</strong></td>
                            <td style="padding: 8px; border: 1px solid #e0e0e0;">将用户输入作为HTML解析并插入DOM</td>
                            <td style="padding: 8px; border: 1px solid #e0e0e0;">HTML标签（如 &lt;img onerror&gt;）</td>
                            <td style="padding: 8px; border: 1px solid #e0e0e0;">⭐⭐⭐ 高</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #e0e0e0;"><strong>document.write</strong></td>
                            <td style="padding: 8px; border: 1px solid #e0e0e0;">直接向文档流写入HTML内容</td>
                            <td style="padding: 8px; border: 1px solid #e0e0e0;">HTML标签（如 &lt;svg onload&gt;）</td>
                            <td style="padding: 8px; border: 1px solid #e0e0e0;">⭐⭐⭐ 高</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #e0e0e0;"><strong>eval</strong></td>
                            <td style="padding: 8px; border: 1px solid #e0e0e0;">将用户输入作为JavaScript代码执行</td>
                            <td style="padding: 8px; border: 1px solid #e0e0e0;"><span style="color: #e65100;">纯JS代码（不需要HTML标签）</span></td>
                            <td style="padding: 8px; border: 1px solid #e0e0e0;">⭐⭐⭐⭐ 极高</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #e0e0e0;"><strong>URL Hash</strong></td>
                            <td style="padding: 8px; border: 1px solid #e0e0e0;">从URL的#后读取数据并写入DOM</td>
                            <td style="padding: 8px; border: 1px solid #e0e0e0;">HTML标签（构造恶意链接传播）</td>
                            <td style="padding: 8px; border: 1px solid #e0e0e0;">⭐⭐⭐ 高</td>
                        </tr>
                    </tbody>
                </table>
                <div style="margin-top: 15px; padding: 10px; background: #fff3e0; border-radius: 5px; border-left: 4px solid #ff9800;">
                    <strong>⚠️ 重要区别：</strong>
                    <ul style="margin: 8px 0 0 20px;">
                        <li><strong>innerHTML/document.write/URL Hash</strong>：需要使用HTML标签注入，如 <code>&lt;img src=x onerror="alert(1)"&gt;</code></li>
                        <li><strong>eval</strong>：直接执行JS代码，只需输入 <code>alert(1)</code>，<span style="color: #e65100;">不要加HTML标签！</span></li>
                        <li><strong>URL Hash</strong>：攻击者可构造恶意链接发送给受害者，隐蔽性高</li>
                    </ul>
                </div>
            </div>
            
            <div class="defense-box">
                <h4>🛡️ 防御方法</h4>
                <ul>
                    <li><strong>避免直接操作DOM</strong>：使用 <code>textContent</code> 替代 <code>innerHTML</code></li>
                    <li><strong>输入验证</strong>：对URL参数、hash等进行严格验证</li>
                    <li><strong>输出编码</strong>：根据上下文使用适当的编码函数</li>
                    <li><strong>使用安全API</strong>：如 <code>DOMPurify</code> 库对HTML进行净化</li>
                    <li><strong>CSP策略</strong>：配置严格的Content-Security-Policy</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Toast提示 -->
    <div id="toast" class="toast"></div>
    
    <script>
        // 显示Toast提示
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast ' + type;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 2000);
        }
        
        // 复制Payload
        function copyPayload(element) {
            let text = element.textContent;
            // 移除可能存在的emoji前缀（如 💡）
            if (text.startsWith('💡 ')) {
                text = text.substring(2).trim();
            }
            navigator.clipboard.writeText(text).then(() => {
                showToast('✅ Payload已复制到剪贴板！', 'success');
            }).catch(() => {
                showToast('❌ 复制失败，请手动复制', 'error');
            });
        }
        
        // 复制文本（用于代码标签）
        function copyText(element) {
            const text = element.textContent;
            navigator.clipboard.writeText(text).then(() => {
                showToast('✅ 已复制: ' + text, 'success');
            }).catch(() => {
                showToast('❌ 复制失败，请手动复制', 'error');
            });
        }
        
        // 清空评论
        function clearComments() {
            if (confirm('确定要清空所有留言吗？')) {
                fetch('clear_xss_comments.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('✅ 留言已清空！', 'success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showToast('❌ 清空失败', 'error');
                        }
                    });
            }
        }
        
        // DOM XSS演示 - innerHTML
        function testInnerHTML() {
            const input = document.getElementById('domInput1').value;
            document.getElementById('output1').innerHTML = '您输入的内容：' + input; // 故意存在XSS漏洞
        }
        
        // DOM XSS演示 - document.write (模拟)
        function testDocWrite() {
            const input = document.getElementById('domInput2').value;
            document.getElementById('output2').innerHTML = '您输入的内容：' + input; // 故意存在XSS漏洞
        }
        
        // DOM XSS演示 - eval
        function testEval() {
            const input = document.getElementById('domInput3').value;
            try {
                eval(input); // 故意存在XSS漏洞
                document.getElementById('output3').innerHTML = '<span style="color: green;">✅ 代码已执行</span>';
            } catch (e) {
                document.getElementById('output3').innerHTML = '<span style="color: red;">❌ 执行错误: ' + e.message + '</span>';
            }
        }
        
        // DOM XSS演示 - URL Hash
        function testHashXSS() {
            const maliciousHash = '#<img src=x onerror="alert(\'Hash XSS\')">';
            window.location.hash = '';
            setTimeout(() => {
                window.location.hash = maliciousHash.substring(1);
                // 读取hash并写入DOM
                const hash = window.location.hash.substring(1);
                if (hash) {
                    document.getElementById('output4').innerHTML = decodeURIComponent(hash); // 故意存在XSS漏洞
                }
            }, 100);
        }
        
        // 页面加载时检查hash
        window.onload = function() {
            const hash = window.location.hash.substring(1);
            if (hash && document.getElementById('output4')) {
                document.getElementById('output4').innerHTML = decodeURIComponent(hash);
            }
        };
    </script>
</body>
</html>