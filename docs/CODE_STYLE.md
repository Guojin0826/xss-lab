# 代码规范

本文档定义了XSS漏洞演示靶场项目的代码规范。

## 📁 目录结构

```
xss/
├── data/                   # 数据存储目录
│   ├── stolen_credentials.txt
│   └── stolen_cookies.txt
├── phishing/               # 钓鱼页面目录
│   └── login.html
├── assets/                 # 静态资源目录
│   ├── css/
│   └── js/
├── demo.php                # 演示首页
├── forum.php               # 论坛页面
├── steal.php               # 凭据接收脚本
├── steal_cookie.php        # Cookie接收脚本
├── view_credentials.php    # 凭据查看页面
├── view_cookies.php        # Cookie查看页面
├── clear_credentials.php   # 清空凭据脚本
├── clear_cookies.php       # 清空Cookie脚本
├── README.md               # 项目说明
├── QUICKSTART.md           # 快速启动指南
├── CONTRIBUTING.md         # 贡献指南
├── SECURITY.md             # 安全政策
├── CHANGELOG.md            # 更新日志
├── LICENSE                 # 许可证
├── .gitignore              # Git忽略文件
└── project.json            # 项目配置
```

## 🎨 编码规范

### PHP代码规范

#### 1. 基本格式
```php
<?php
// 文件头部注释
/**
 * 文件描述
 * 
 * @package XSS_Lab
 * @author  作者名
 * @since   1.0.0
 */

// 使用4个空格缩进
function exampleFunction($param1, $param2) {
    // 函数体
    if ($condition) {
        // 代码块
    }
}
```

#### 2. 命名规范
- **变量**: 小驼峰命名 `$userName`
- **常量**: 全大写下划线 `MAX_SIZE`
- **函数**: 小驼峰命名 `getUserData()`
- **类**: 大驼峰命名 `UserManager`

#### 3. 安全规范
```php
// ❌ 不安全
$data = $_GET['data'];
echo $data;

// ✅ 安全
$data = htmlspecialchars($_GET['data'], ENT_QUOTES, 'UTF-8');
echo $data;
```

#### 4. 注释规范
```php
/**
 * 函数说明
 * 
 * @param string $param 参数说明
 * @return array 返回值说明
 */
function functionName($param) {
    // 单行注释
    
    /*
     * 多行注释
     * 详细说明
     */
}
```

### HTML代码规范

#### 1. 文档结构
```html
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>页面标题</title>
</head>
<body>
    <!-- 页面内容 -->
</body>
</html>
```

#### 2. 缩进与格式
- 使用4个空格缩进
- 标签正确闭合
- 属性值使用双引号

#### 3. 语义化标签
```html
<!-- ✅ 推荐 -->
<header></header>
<nav></nav>
<main></main>
<footer></footer>

<!-- ❌ 不推荐 -->
<div class="header"></div>
<div class="nav"></div>
```

### CSS代码规范

#### 1. 选择器命名
```css
/* 使用小写字母和连字符 */
.user-profile { }
.login-form { }

/* 避免过深嵌套 */
.container .user .profile .name { } /* ❌ */
.user-name { } /* ✅ */
```

#### 2. 属性顺序
```css
.selector {
    /* 定位 */
    position: absolute;
    top: 0;
    left: 0;
    
    /* 盒模型 */
    display: flex;
    width: 100px;
    height: 100px;
    padding: 10px;
    margin: 10px;
    
    /* 视觉 */
    background: #fff;
    border: 1px solid #ccc;
    color: #333;
    
    /* 其他 */
    cursor: pointer;
}
```

### JavaScript代码规范

#### 1. 变量声明
```javascript
// 使用const和let
const API_URL = 'https://api.example.com';
let userData = {};

// 避免使用var
var name = 'test'; // ❌
```

#### 2. 函数定义
```javascript
// 箭头函数
const handleClick = () => {
    // 函数体
};

// 普通函数
function processData(data) {
    return data;
}
```

#### 3. 注释
```javascript
/**
 * 函数说明
 * @param {string} param 参数说明
 * @returns {boolean} 返回值说明
 */
function functionName(param) {
    // 单行注释
}
```

## 🔒 安全规范

### 1. 文件权限
```bash
# 数据目录
chmod 777 data/

# PHP文件
chmod 644 *.php

# 配置文件
chmod 600 config.php
```

### 2. 输入验证
```php
// 始终验证用户输入
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
```

### 3. 输出转义
```php
// HTML输出
echo htmlspecialchars($data, ENT_QUOTES, 'UTF-8');

// JavaScript输出
echo json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
```

### 4. 文件操作
```php
// 检查文件路径
$path = realpath($path);
if (strpos($path, '/allowed/directory/') !== 0) {
    die('非法路径');
}
```

## 📝 提交规范

### Commit Message格式
```
<type>(<scope>): <subject>

<body>

<footer>
```

### Type类型
- `feat`: 新功能
- `fix`: 修复Bug
- `docs`: 文档更新
- `style`: 代码格式
- `refactor`: 重构
- `test`: 测试
- `chore`: 构建/工具

### 示例
```
feat(forum): 添加删除评论功能

- 添加删除按钮UI
- 实现删除逻辑
- 添加确认对话框

Closes #123
```

## 🧪 测试规范

### 1. 功能测试
- 测试所有表单提交
- 测试所有按钮点击
- 测试边界情况

### 2. 安全测试
- 测试XSS漏洞
- 测试CSRF漏洞
- 测试文件上传

### 3. 兼容性测试
- Chrome浏览器
- Firefox浏览器
- Edge浏览器
- Safari浏览器

## 📚 参考资料

- [PHP编码规范](https://www.php-fig.org/psr/)
- [HTML规范](https://html.spec.whatwg.org/)
- [CSS规范](https://cssguidelin.es/)
- [JavaScript规范](https://standardjs.com/)

---

**遵循这些规范，让代码更优雅、更安全！**