<?php
/**
 * XSS防御演示模块
 * 展示各种XSS防御技术和方法
 * 
 * @author  Guojin0826
 * @email   jinrcsy@gmail.com
 * @version 1.0.0
 */

// 处理演示表单提交
$demoResult = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['demo_type'])) {
    $input = $_POST['user_input'] ?? '';
    $demoType = $_POST['demo_type'];
    
    switch ($demoType) {
        case 'no_filter':
            $demoResult = [
                'title' => '❌ 无过滤（危险）',
                'input' => $input,
                'output' => $input,
                'safe' => false,
                'description' => '直接输出用户输入，存在XSS漏洞'
            ];
            break;
            
        case 'htmlspecialchars':
            $demoResult = [
                'title' => '✅ htmlspecialchars过滤',
                'input' => $input,
                'output' => htmlspecialchars($input, ENT_QUOTES, 'UTF-8'),
                'safe' => true,
                'description' => '使用htmlspecialchars转义HTML特殊字符'
            ];
            break;
            
        case 'strip_tags':
            $demoResult = [
                'title' => '✅ strip_tags过滤',
                'input' => $input,
                'output' => strip_tags($input),
                'safe' => true,
                'description' => '移除所有HTML和PHP标签'
            ];
            break;
            
        case 'htmlentities':
            $demoResult = [
                'title' => '✅ htmlentities过滤',
                'input' => $input,
                'output' => htmlentities($input, ENT_QUOTES, 'UTF-8'),
                'safe' => true,
                'description' => '转换所有适用的字符为HTML实体'
            ];
            break;
            
        case 'custom_filter':
            // 自定义过滤函数
            $filtered = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
            $filtered = preg_replace('/on\w+\s*=/i', 'data-blocked=', $filtered);
            $demoResult = [
                'title' => '⚠️ 自定义过滤',
                'input' => $input,
                'output' => $filtered,
                'safe' => true,
                'description' => '使用htmlspecialchars + 移除事件处理器'
            ];
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XSS防御演示 - XSS Lab</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .header p {
            font-size: 1.1em;
            opacity: 0.9;
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
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .card h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.8em;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        
        .card p {
            color: #666;
            line-height: 1.8;
            margin-bottom: 15px;
        }
        
        .filter-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .filter-option {
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .filter-option:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .filter-option.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }
        
        .filter-option input[type="radio"] {
            margin-right: 12px;
            cursor: pointer;
            width: 20px;
            height: 20px;
            vertical-align: middle;
            accent-color: #667eea;
        }
        
        .filter-option label {
            cursor: pointer;
            font-weight: 500;
            display: inline-block;
            vertical-align: middle;
            user-select: none;
            line-height: 1.4;
        }
        
        .filter-option.unsafe {
            border-color: #ff6b6b;
            background: #fff5f5;
        }
        
        .filter-option.safe {
            border-color: #4ecdc4;
            background: #f0fffe;
        }
        
        .filter-option.unsafe.selected {
            background: #ffe5e5;
            border-color: #ff4757;
        }
        
        .filter-option.safe.selected {
            background: #e0fff8;
            border-color: #26de81;
        }
        
        .demo-form {
            margin-top: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-group input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-test {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-test:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .result-box {
            margin-top: 25px;
            padding: 20px;
            border-radius: 10px;
            background: #f8f9fa;
            border-left: 4px solid #667eea;
        }
        
        .result-box.unsafe {
            border-left-color: #ff6b6b;
            background: #fff5f5;
        }
        
        .result-box.safe {
            border-left-color: #4ecdc4;
            background: #f0fffe;
        }
        
        .result-item {
            margin: 15px 0;
        }
        
        .result-item label {
            font-weight: 600;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }
        
        .result-item .value {
            background: white;
            padding: 10px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }
        
        .tips {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }
        
        .tips h4 {
            color: #856404;
            margin-bottom: 10px;
        }
        
        .tips p {
            color: #856404;
            margin: 0;
        }
        
        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .comparison-table th,
        .comparison-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .comparison-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        .comparison-table code {
            background: #f8f9fa;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 13px;
        }
        
        .badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge.safe {
            background: #d4edda;
            color: #155724;
        }
        
        .badge.unsafe {
            background: #f8d7da;
            color: #721c24;
        }
        
        .comparison-table button {
            background: #667eea;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .comparison-table button:hover {
            background: #764ba2;
            transform: translateY(-1px);
        }
        
        .defense-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .defense-item {
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
        }
        
        .defense-item.success {
            border-color: #4ecdc4;
            background: #f0fffe;
        }
        
        .defense-item h3 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .defense-item p {
            color: #666;
            margin-bottom: 15px;
        }
        
        .code-block {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
            overflow-x: auto;
        }
        
        .code-block .comment {
            color: #6272a4;
        }
        
        .code-block .keyword {
            color: #ff79c6;
        }
        
        .code-block .function {
            color: #50fa7b;
        }
        
        .code-block .string {
            color: #f1fa8c;
        }
        
        ul {
            margin-left: 20px;
        }
        
        ul li {
            margin: 8px 0;
            color: #666;
        }
        
        ul li strong {
            color: #333;
        }
    </style>
</head>
<body>
    <a href="demo.php" class="back-btn">🏠 返回演示首页</a>
    
    <div class="container">
        <div class="header">
            <h1>🛡️ XSS防御演示</h1>
            <p>学习如何正确防御XSS攻击，保护Web应用安全</p>
        </div>
        
        <!-- 输入过滤演示 -->
        <div class="card">
            <h2>📥 输入过滤演示</h2>
            <p>选择不同的过滤方法，输入测试数据，查看过滤效果：</p>
            
            <form method="POST" class="demo-form">
                <div class="filter-options">
                    <div class="filter-option unsafe">
                        <input type="radio" name="demo_type" id="no_filter" value="no_filter" required>
                        <label for="no_filter">❌ 无过滤（危险）</label>
                    </div>
                    
                    <div class="filter-option safe">
                        <input type="radio" name="demo_type" id="htmlspecialchars" value="htmlspecialchars" checked>
                        <label for="htmlspecialchars">✅ htmlspecialchars</label>
                    </div>
                    
                    <div class="filter-option safe">
                        <input type="radio" name="demo_type" id="strip_tags" value="strip_tags">
                        <label for="strip_tags">✅ strip_tags</label>
                    </div>
                    
                    <div class="filter-option safe">
                        <input type="radio" name="demo_type" id="htmlentities" value="htmlentities">
                        <label for="htmlentities">✅ htmlentities</label>
                    </div>
                    
                    <div class="filter-option safe">
                        <input type="radio" name="demo_type" id="custom_filter" value="custom_filter">
                        <label for="custom_filter">⚠️ 自定义过滤</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="user_input">测试输入：</label>
                    <input type="text" id="user_input" name="user_input" 
                           placeholder="输入测试Payload，例如：<script>alert('XSS')</script>"
                           value="<?php echo isset($_POST['user_input']) ? htmlspecialchars($_POST['user_input']) : ''; ?>">
                </div>
                
                <button type="submit" class="btn-test">🧪 测试过滤效果</button>
            </form>
            
            <?php if ($demoResult): ?>
            <div class="result-box <?php echo $demoResult['safe'] ? 'safe' : 'unsafe'; ?>">
                <h3><?php echo $demoResult['title']; ?></h3>
                <p><?php echo $demoResult['description']; ?></p>
                
                <div class="result-item">
                    <label>原始输入：</label>
                    <div class="value"><?php echo htmlspecialchars($demoResult['input']); ?></div>
                </div>
                
                <div class="result-item">
                    <label>过滤后输出：</label>
                    <div class="value"><?php echo htmlspecialchars($demoResult['output']); ?></div>
                </div>
                
                <div class="result-item">
                    <label>实际渲染效果：</label>
                    <div class="value" style="background: #fff; padding: 15px; border: 2px dashed #ddd;">
                        <?php echo $demoResult['output']; ?>
                    </div>
                </div>
                
                <?php if (!$demoResult['safe']): ?>
                <div class="tips">
                    <h4>⚠️ 安全警告</h4>
                    <p>当前方法存在安全风险！建议使用htmlspecialchars或htmlentities进行防御。</p>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- 常见XSS Payload防御对比 -->
        <div class="card">
            <h2>🎯 常见XSS Payload防御对比</h2>
            <p>查看不同Payload在不同防御方法下的防御效果对比：</p>
            
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th style="width: 15%">Payload类型</th>
                        <th style="width: 25%">Payload内容</th>
                        <th style="width: 12%">无过滤</th>
                        <th style="width: 12%">htmlspecialchars</th>
                        <th style="width: 12%">strip_tags</th>
                        <th style="width: 12%">htmlentities</th>
                        <th style="width: 12%">自定义过滤</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // 定义测试Payload
                    $testPayloads = [
                        [
                            'type' => 'Script标签',
                            'payload' => "<script>alert('XSS')</script>",
                            'display' => "&lt;script&gt;alert('XSS')&lt;/script&gt;"
                        ],
                        [
                            'type' => '事件处理器',
                            'payload' => "<img src=x onerror=alert('XSS')>",
                            'display' => "&lt;img src=x onerror=alert('XSS')&gt;"
                        ],
                        [
                            'type' => 'SVG标签',
                            'payload' => "<svg onload=alert('XSS')>",
                            'display' => "&lt;svg onload=alert('XSS')&gt;"
                        ],
                        [
                            'type' => 'JavaScript伪协议',
                            'payload' => "<a href=javascript:alert('XSS')>click</a>",
                            'display' => "&lt;a href=javascript:alert('XSS')&gt;click&lt;/a&gt;"
                        ],
                        [
                            'type' => 'HTML实体编码',
                            'payload' => "&lt;script&gt;alert('XSS')&lt;/script&gt;",
                            'display' => "&amp;lt;script&amp;gt;alert('XSS')&amp;lt;/script&amp;gt;"
                        ],
                        [
                            'type' => 'Style标签',
                            'payload' => "<style>body{background:red}</style>",
                            'display' => "&lt;style&gt;body{background:red}&lt;/style&gt;"
                        ],
                        [
                            'type' => 'Iframe标签',
                            'payload' => "<iframe src='javascript:alert(\"XSS\")'>",
                            'display' => "&lt;iframe src='javascript:alert(\"XSS\")'&gt;"
                        ],
                        [
                            'type' => 'Div事件',
                            'payload' => "<div onmouseover=alert('XSS')>hover</div>",
                            'display' => "&lt;div onmouseover=alert('XSS')&gt;hover&lt;/div&gt;"
                        ]
                    ];
                    
                    foreach ($testPayloads as $item):
                        $payload = $item['payload'];
                        
                        // 测试各种过滤方法
                        $noFilter = $payload;
                        $htmlspecialchars = htmlspecialchars($payload, ENT_QUOTES, 'UTF-8');
                        $stripTags = strip_tags($payload);
                        $htmlentities = htmlentities($payload, ENT_QUOTES, 'UTF-8');
                        
                        // 自定义过滤
                        $customFilter = htmlspecialchars($payload, ENT_QUOTES, 'UTF-8');
                        $customFilter = preg_replace('/on\w+\s*=/i', 'data-blocked=', $customFilter);
                        
                        // 判断是否安全（检查是否包含可执行的XSS）
                        $isNoFilterSafe = !preg_match('/<script|onerror|onload|onmouseover|javascript:/i', $noFilter);
                        $isHtmlspecialcharsSafe = true; // htmlspecialchars总是安全的
                        $isStripTagsSafe = !preg_match('/javascript:|onerror|onload|onmouseover/i', $stripTags);
                        $isHtmlentitiesSafe = true; // htmlentities总是安全的
                        $isCustomFilterSafe = true; // 自定义过滤总是安全的
                    ?>
                    <tr>
                        <td><strong><?php echo $item['type']; ?></strong></td>
                        <td><code><?php echo $item['display']; ?></code></td>
                        <td><span class="badge <?php echo $isNoFilterSafe ? 'safe' : 'unsafe'; ?>"><?php echo $isNoFilterSafe ? '✅ 安全' : '❌ 危险'; ?></span></td>
                        <td><span class="badge safe">✅ 安全</span></td>
                        <td><span class="badge <?php echo $isStripTagsSafe ? 'safe' : 'unsafe'; ?>"><?php echo $isStripTagsSafe ? '✅ 安全' : '⚠️ 部分'; ?></span></td>
                        <td><span class="badge safe">✅ 安全</span></td>
                        <td><span class="badge safe">✅ 安全</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="tips" style="margin-top: 20px;">
                <h4>📊 防御方法对比说明</h4>
                <ul>
                    <li><strong style="color: #dc3545;">❌ 无过滤</strong> - 直接输出用户输入，所有XSS Payload都能执行，极度危险</li>
                    <li><strong style="color: #28a745;">✅ htmlspecialchars</strong> - 转义HTML特殊字符，防御所有HTML XSS，推荐使用</li>
                    <li><strong style="color: #ffc107;">⚠️ strip_tags</strong> - 移除HTML标签，但无法防御JavaScript伪协议和某些事件处理器</li>
                    <li><strong style="color: #28a745;">✅ htmlentities</strong> - 转义所有HTML实体，防御所有HTML XSS，最安全</li>
                    <li><strong style="color: #28a745;">✅ 自定义过滤</strong> - htmlspecialchars + 移除事件处理器，额外安全层</li>
                </ul>
            </div>
        </div>
        
        <!-- 最佳实践 -->
        <div class="card">
            <h2>💡 XSS防御最佳实践</h2>
            
            <div class="defense-grid">
                <div class="defense-item success">
                    <h3>1️⃣ 输入验证</h3>
                    <p>验证所有用户输入，确保符合预期格式。</p>
                    <div class="code-block">
                        <span class="comment">// 白名单验证</span><br>
                        <span class="keyword">if</span> (<span class="function">preg_match</span>(<span class="string">'/^[a-zA-Z0-9]+$/'</span>, $input)) {<br>
                        &nbsp;&nbsp;<span class="comment">// 安全处理</span><br>
                        }
                    </div>
                </div>
                
                <div class="defense-item success">
                    <h3>2️⃣ 输出编码</h3>
                    <p>根据输出位置选择合适的编码方式。</p>
                    <div class="code-block">
                        <span class="comment">// HTML内容</span><br>
                        <span class="function">htmlspecialchars</span>($input, ENT_QUOTES);<br><br>
                        <span class="comment">// JavaScript中</span><br>
                        <span class="function">json_encode</span>($input);
                    </div>
                </div>
                
                <div class="defense-item success">
                    <h3>3️⃣ 使用CSP</h3>
                    <p>配置内容安全策略，限制资源加载。</p>
                    <div class="code-block">
                        <span class="function">header</span>(<span class="string">"Content-Security-Policy: "</span><br>
                        &nbsp;&nbsp;.<span class="string">"default-src 'self'; "</span><br>
                        &nbsp;&nbsp;.<span class="string">"script-src 'self'"</span>);
                    </div>
                </div>
                
                <div class="defense-item success">
                    <h3>4️⃣ Cookie安全</h3>
                    <p>设置HttpOnly和Secure标志。</p>
                    <div class="code-block">
                        <span class="function">setcookie</span>(<span class="string">'name'</span>, $value, [<br>
                        &nbsp;&nbsp;<span class="string">'httponly'</span> => <span class="keyword">true</span>,<br>
                        &nbsp;&nbsp;<span class="string">'secure'</span> => <span class="keyword">true</span><br>
                        ]);
                    </div>
                </div>
            </div>
            
            <div class="tips">
                <h4>📌 防御要点总结</h4>
                <ul>
                    <li><strong>永远不要信任用户输入</strong> - 所有输入都是潜在的危险</li>
                    <li><strong>输入过滤 + 输出编码</strong> - 双重保护机制</li>
                    <li><strong>使用成熟的安全函数</strong> - 不要自己造轮子</li>
                    <li><strong>设置CSP策略</strong> - 深度防御措施</li>
                    <li><strong>定期安全审计</strong> - 持续改进安全措施</li>
                </ul>
            </div>
        </div>
    </div>
    
    <script>
        // 标签页切换
        function switchTab(tabId) {
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            document.querySelector(`.tab[onclick="switchTab('${tabId}')"]`).classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }
        
        // 处理过滤选项的选中状态
        document.addEventListener('DOMContentLoaded', function() {
            const filterOptions = document.querySelectorAll('.filter-option');
            
            // 初始化选中状态
            filterOptions.forEach(option => {
                const radio = option.querySelector('input[type="radio"]');
                if (radio.checked) {
                    option.classList.add('selected');
                }
            });
            
            // 监听所有radio的change事件
            document.querySelectorAll('input[name="demo_type"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    // 移除所有选中状态
                    filterOptions.forEach(opt => opt.classList.remove('selected'));
                    
                    // 添加当前选中状态
                    if (this.checked) {
                        this.closest('.filter-option').classList.add('selected');
                    }
                });
            });
        });
        
        // Payload测试按钮
        document.querySelectorAll('.test-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const payload = this.getAttribute('data-payload');
                document.querySelector('input[name="user_input"]').value = payload;
                document.querySelector('input[name="demo_type"][value="htmlspecialchars"]').checked = true;
                
                // 触发选中状态更新
                const event = new Event('change', { bubbles: true });
                document.querySelector('input[name="demo_type"][value="htmlspecialchars"]').dispatchEvent(event);
                
                document.querySelector('form').submit();
            });
        });
    </script>
</body>
</html>