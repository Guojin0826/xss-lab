# 快速启动指南

## 📸 项目预览

![演示入口](../img/demo1.png)

## 🚀 5分钟快速部署

### 前置要求
- PHP 5.6 或更高版本
- Apache/Nginx Web服务器
- 现代浏览器（Chrome、Firefox、Edge等）

### 部署步骤

#### 方法一：使用PHP内置服务器（推荐用于测试）

```bash
# 1. 进入项目目录
cd xss

# 2. 启动PHP内置服务器
php -S localhost:8000

# 3. 打开浏览器访问
# http://localhost:8000/demo.php
```

#### 方法二：使用Apache

```bash
# 1. 将项目复制到Web目录
cp -r xss /var/www/html/

# 2. 设置目录权限
chmod -R 755 /var/www/html/xss
chmod -R 777 /var/www/html/xss/data

# 3. 访问
# http://localhost/xss/demo.php
```

#### 方法三：使用XAMPP/WAMP（Windows）

```
1. 安装XAMPP或WAMP
2. 将xss文件夹复制到htdocs目录
3. 启动Apache服务
4. 访问 http://localhost/xss/demo.php
```

### 目录权限设置

确保data目录可写：
```bash
chmod -R 777 data/
```

### 验证安装

访问以下页面确认安装成功：
- ✅ 演示首页：`demo.php`
- ✅ 论坛页面：`forum.php`
- ✅ 凭据查看：`view_credentials.php`
- ✅ Cookie查看：`view_cookies.php`

## 📖 使用教程

### 1. 钓鱼攻击演示

1. 访问 `forum.php`
2. 在评论区输入钓鱼Payload（见demo.php）
3. 刷新页面触发跳转
4. 在钓鱼页面输入凭据
5. 访问 `view_credentials.php` 查看窃取的数据

### 2. Cookie窃取演示

1. 访问 `forum.php`
2. 点击"触发XSS攻击"按钮
3. Cookie自动发送到服务器
4. 访问 `view_cookies.php` 查看窃取的Cookie

### 3. 清除测试数据

- 清除凭据：访问 `clear_credentials.php`
- 清除Cookie：访问 `clear_cookies.php`

## ⚠️ 安全警告

**本平台仅用于安全教学和演示！**

- ❌ 请勿部署到公网服务器
- ❌ 请勿用于攻击真实网站
- ❌ 请勿窃取真实用户数据
- ✅ 仅在本地隔离环境中使用
- ✅ 用于学习和研究目的

## 🔧 常见问题

### Q: 页面无法访问？
A: 检查Web服务器是否启动，PHP是否正确安装

### Q: 数据无法保存？
A: 确保data目录有写入权限（chmod 777 data/）

### Q: XSS不触发？
A: 检查浏览器是否启用了XSS防护，建议使用隐私模式

### Q: 中文乱码？
A: 确保所有文件使用UTF-8编码保存

## 📞 技术支持

遇到问题？请查看：
- [README.md](README.md) - 完整文档
- [CONTRIBUTING.md](CONTRIBUTING.md) - 贡献指南
- GitHub Issues - 提交问题

---

**祝学习愉快！🎉**