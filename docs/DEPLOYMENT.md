# 🚀 XSS Lab - 部署指南

本指南将帮助你快速部署XSS Lab到本地环境或服务器。

---

## 📋 目录

- [环境要求](#环境要求)
- [快速部署](#快速部署)
- [详细部署步骤](#详细部署步骤)
- [配置说明](#配置说明)
- [常见问题](#常见问题)
- [安全建议](#安全建议)

---

## 📦 环境要求

### 必需环境
- **PHP**: 7.4 或更高版本
- **Web服务器**: Apache 或 Nginx
- **浏览器**: Chrome, Firefox, Safari, Edge (现代浏览器)

### 推荐环境
- **XAMPP** (Windows/Mac/Linux)
- **WAMP** (Windows)
- **MAMP** (Mac)
- **LAMP** (Linux)
- **Docker** (跨平台)

---

## ⚡ 快速部署

### 方式一：PHP内置服务器（最简单）

```bash
# 1. 克隆项目
git clone https://github.com/Guojin0826/xss-lab.git

# 2. 进入项目目录
cd xss-lab

# 3. 启动PHP内置服务器
php -S localhost:8000

# 4. 访问项目
# 打开浏览器访问: http://localhost:8000/demo.php
# 或访问Payload库: http://localhost:8000/payload_library.php
# 或访问防御演示: http://localhost:8000/defense_demo.php
```

### 方式二：XAMPP部署

```bash
# 1. 下载并安装XAMPP
# https://www.apachefriends.org/

# 2. 克隆项目到htdocs目录
cd C:\xampp\htdocs  # Windows
# 或
cd /Applications/XAMPP/htdocs  # Mac
# 或
cd /opt/lampp/htdocs  # Linux

git clone https://github.com/Guojin0826/xss-lab.git

# 3. 启动XAMPP控制面板，启动Apache

# 4. 访问项目
# 打开浏览器访问: http://localhost/xss-lab/demo.php
```

### 方式三：Docker部署

```bash
# 1. 创建Dockerfile
cat > Dockerfile << 'EOF'
FROM php:7.4-apache

# 启用Apache mod_rewrite
RUN a2enmod rewrite

# 设置工作目录
WORKDIR /var/www/html

# 复制项目文件
COPY . /var/www/html/

# 设置权限
RUN chown -R www-data:www-data /var/www/html

# 暴露端口
EXPOSE 80

# 启动Apache
CMD ["apache2-foreground"]
EOF

# 2. 构建镜像
docker build -t xss-lab .

# 3. 运行容器
docker run -d -p 8080:80 --name xss-lab-container xss-lab

# 4. 访问项目
# 打开浏览器访问: http://localhost:8080/demo.php
```

---

## 📝 详细部署步骤

### 步骤1：获取项目代码

```bash
# 方式1: Git克隆（推荐）
git clone https://github.com/Guojin0826/xss-lab.git

# 方式2: 下载ZIP
# 访问 https://github.com/Guojin0826/xss-lab
# 点击 "Code" -> "Download ZIP"
# 解压到目标目录
```

### 步骤2：配置Web服务器

#### Apache配置

在项目根目录创建 `.htaccess` 文件：

```apache
# 启用重写引擎
RewriteEngine On

# 设置基础目录
RewriteBase /xss-lab/

# 防止目录浏览
Options -Indexes

# 设置默认页面
DirectoryIndex demo.php

# PHP设置
<IfModule mod_php7.c>
    php_flag display_errors On
    php_value error_reporting E_ALL
</IfModule>

# 安全设置
<FilesMatch "\.(txt|log|json)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

#### Nginx配置

创建Nginx配置文件：

```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/html/xss-lab;
    index demo.php index.php;

    location / {
        try_files $uri $uri/ /demo.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # 禁止访问敏感文件
    location ~ /\.(txt|log|json)$ {
        deny all;
    }
}
```

### 步骤3：设置目录权限

```bash
# Linux/Mac
chmod -R 755 xss-lab/
chmod -R 777 xss-lab/data/

# Windows (以管理员身份运行)
icacls xss-lab\data /grant Everyone:(OI)(CI)F
```

### 步骤4：配置项目（可选）

复制配置示例文件：

```bash
cp config.example.php config.php
```

编辑 `config.php`：

```php
<?php
// 项目配置
define('SITE_NAME', 'XSS Lab');
define('SITE_URL', 'http://localhost:8000');

// 数据存储路径
define('DATA_PATH', __DIR__ . '/data/');

// 安全设置
define('ALLOWED_IPS', []); // 留空表示允许所有IP

// 调试模式
define('DEBUG_MODE', true);
?>
```

### 步骤5：验证部署

访问以下URL验证部署是否成功：

- 演示首页: `http://localhost:8000/demo.php`
- 论坛页面: `http://localhost:8000/forum.php`
- 凭据查看: `http://localhost:8000/view_credentials.php`
- Cookie查看: `http://localhost:8000/view_cookies.php`

---

## ⚙️ 配置说明

### PHP配置建议

编辑 `php.ini` 文件：

```ini
; 错误显示（开发环境）
display_errors = On
error_reporting = E_ALL

; 文件上传
file_uploads = On
upload_max_filesize = 10M
post_max_size = 10M

; 会话设置
session.save_path = "/tmp"
session.use_cookies = 1
session.use_only_cookies = 1

; 安全设置
expose_php = Off
```

### Apache虚拟主机配置

```apache
<VirtualHost *:80>
    ServerName xss-lab.local
    DocumentRoot "C:/xampp/htdocs/xss-lab"
    
    <Directory "C:/xampp/htdocs/xss-lab">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/xss-lab-error.log"
    CustomLog "logs/xss-lab-access.log" common
</VirtualHost>
```

然后在hosts文件中添加：

```
127.0.0.1 xss-lab.local
```

---

## ❓ 常见问题

### Q1: 页面显示空白或500错误

**解决方案：**
```php
// 在PHP文件开头添加错误显示
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

### Q2: 无法写入数据文件

**解决方案：**
```bash
# 检查data目录权限
ls -la data/

# 修改权限
chmod -R 777 data/

# 或修改所有者
chown -R www-data:www-data data/
```

### Q3: XSS Payload不触发

**解决方案：**
1. 检查浏览器是否禁用了JavaScript
2. 检查Content Security Policy (CSP)设置
3. 清除浏览器缓存
4. 使用无痕/隐私模式测试

### Q4: 无法跳转到钓鱼页面

**解决方案：**
1. 检查`phishing/login.html`文件是否存在
2. 检查URL路径是否正确
3. 检查浏览器是否阻止了弹窗/跳转

### Q5: Cookie无法窃取

**解决方案：**
1. 确认Cookie存在（检查浏览器开发者工具）
2. 检查`steal_cookie.php`文件路径
3. 检查`data/`目录权限
4. 检查浏览器是否阻止了第三方请求

---

## 🔒 安全建议

### 开发环境安全

1. **仅本地访问**
   ```php
   // 在config.php中限制IP
   define('ALLOWED_IPS', ['127.0.0.1', '::1']);
   ```

2. **禁用外部访问**
   ```bash
   # 仅监听本地端口
   php -S 127.0.0.1:8000
   ```

3. **使用防火墙**
   ```bash
   # Windows防火墙
   netsh advfirewall firewall add rule name="XSS Lab" dir=in action=block protocol=TCP localport=8000 remoteip=any
   
   # Linux防火墙
   sudo ufw deny 8000
   sudo ufw allow from 127.0.0.1 to any port 8000
   ```

### 生产环境安全

⚠️ **警告：强烈不建议在生产环境部署此项目！**

如果必须部署，请：

1. **添加身份验证**
   ```php
   session_start();
   if (!isset($_SESSION['authenticated'])) {
       header('Location: login.php');
       exit;
   }
   ```

2. **限制访问IP**
   ```php
   $allowed_ips = ['192.168.1.100'];
   if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
       die('Access denied');
   }
   ```

3. **使用HTTPS**
   ```apache
   # 强制HTTPS
   RewriteEngine On
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

4. **定期清理数据**
   ```bash
   # 添加定时任务清理数据
   0 0 * * * rm -rf /var/www/html/xss-lab/data/*.txt
   ```

---

## 📊 性能优化

### PHP优化

```ini
; opcache设置
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
```

### Apache优化

```apache
# 启用压缩
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css application/javascript
</IfModule>

# 启用缓存
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 week"
    ExpiresByType application/javascript "access plus 1 week"
</IfModule>
```

---

## 🔄 更新部署

```bash
# 1. 备份数据
cp -r data/ data_backup/

# 2. 拉取最新代码
git pull origin main

# 3. 恢复数据
cp -r data_backup/* data/

# 4. 清除缓存
rm -rf .git/cache/
```

---

## 📞 获取帮助

如果遇到问题：

1. 📖 查看 [README.md](README.md)
2. 📖 查看 [QUICKSTART.md](QUICKSTART.md)
3. 🐛 提交 [Issue](https://github.com/Guojin0826/xss-lab/issues)
4. 💬 参与 [讨论](https://github.com/Guojin0826/xss-lab/discussions)

---

## 🎉 完成！

恭喜你成功部署了XSS Lab！

现在可以开始探索XSS漏洞的世界了。记住：

- ⚠️ **仅用于教育目的**
- 🔒 **不要部署到公网**
- 📚 **负责任地使用知识**

祝你学习愉快！🚀