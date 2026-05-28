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
- ✅ XSS类型演示：`xss_types_demo.php`
- ✅ 论坛页面：`forum.php`
- ✅ 键盘记录演示：`keylogger_demo.php`
- ✅ Payload库：`payload_library.php`
- ✅ 防御演示：`defense_demo.php`
- ✅ 数据查看器：`viewer.php`

## 📖 使用教程

### 1. XSS类型学习（入门）

1. 访问 `xss_types_demo.php`
2. 学习反射型、存储型、DOM型XSS的原理
3. 使用提供的Payload进行测试
4. 查看防御建议

### 2. 钓鱼攻击演示

1. 访问 `forum.php`
2. 在评论区输入钓鱼Payload（见demo.php）
3. 刷新页面触发跳转
4. 在钓鱼页面输入凭据
5. 访问 `viewer.php` 查看窃取的数据

### 3. Cookie窃取演示

1. 访问 `forum.php`
2. 点击"触发XSS攻击"按钮
3. Cookie自动发送到服务器
4. 访问 `viewer.php` 查看窃取的Cookie

### 4. 键盘记录演示

1. 访问 `keylogger_demo.php`
2. 在输入框中输入内容
3. 实时查看键盘记录
4. 访问 `viewer.php` 查看记录数据

### 5. Payload库使用

1. 访问 `payload_library.php`
2. 浏览或搜索Payload
3. 点击"复制"按钮复制Payload
4. 点击"测试"按钮测试Payload效果

### 6. XSS防御演示

1. 访问 `defense_demo.php`
2. 查看不同防御方法的对比
3. 输入测试内容查看过滤效果
4. 学习防御代码实现

### 7. 清除测试数据

在演示首页 `demo.php` 中点击相应按钮即可清空数据：
- 清空凭据数据
- 清空Cookie数据
- 清空键盘记录
- 清空论坛评论
- 清空XSS演示评论

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