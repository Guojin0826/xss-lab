# 🛡️ XSS防御演示模块

## 📸 模块截图

### 防御演示主页面
![防御演示主页面](../img/defense_demo1.png)

### 防御方法对比
![防御方法对比](../img/defense_demo2.png)

## 📋 模块概述

XSS防御演示模块提供了全面的防御方案教学，帮助开发者了解如何正确防御XSS攻击。

## 🎯 功能特点

### 1. 输入过滤演示
- **htmlspecialchars()** - HTML实体编码
- **strip_tags()** - 移除HTML标签
- **自定义过滤函数** - 灵活的过滤规则

### 2. 输出编码演示
- **HTML编码** - 防止HTML注入
- **JavaScript编码** - 防止JS注入
- **URL编码** - 防止URL注入

### 3. CSP策略配置
- **Content-Security-Policy** - 内容安全策略
- **script-src** - 脚本来源限制
- **default-src** - 默认资源限制

### 4. Cookie安全设置
- **HTTPOnly** - 防止JavaScript访问
- **Secure** - 仅HTTPS传输
- **SameSite** - 跨站限制

### 5. 安全实践对比
- **有漏洞代码** - 展示常见错误
- **安全代码** - 展示正确做法
- **实时效果对比** - 可视化展示差异

## 🚀 使用方法

### 第一步：访问防御演示页面
```
http://localhost/xss-lab/defense_demo.php
```

### 第二步：选择防御方法
点击不同的防御方法标签页：
- 输入过滤
- 输出编码
- CSP策略
- Cookie安全

### 第三步：查看代码示例
每个方法都提供：
- ❌ 有漏洞的代码示例
- ✅ 安全的代码示例
- 📝 详细说明和注释

### 第四步：实时测试
- 输入测试数据
- 查看过滤效果
- 对比安全与不安全的差异

## 📊 防御方法详解

### 方法1：输入过滤

**htmlspecialchars()函数**
```php
// ❌ 不安全：直接使用用户输入
echo $_GET['input'];

// ✅ 安全：使用htmlspecialchars过滤
echo htmlspecialchars($_GET['input'], ENT_QUOTES, 'UTF-8');
```

**效果：**
- `<script>` → `&lt;script&gt;`
- 防止HTML标签执行
- 保留原始内容显示

### 方法2：输出编码

**JavaScript编码**
```javascript
// ❌ 不安全：直接输出到JS
var data = '<?php echo $user_input; ?>';

// ✅ 安全：使用json_encode
var data = <?php echo json_encode($user_input); ?>;
```

**效果：**
- 特殊字符被转义
- 防止JS注入
- 安全的数据传递

### 方法3：CSP策略

**Content-Security-Policy**
```php
// ✅ 设置CSP头
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.example.com");
```

**效果：**
- 限制脚本来源
- 阻止外部脚本加载
- 减少XSS攻击面

### 方法4：Cookie安全

**安全Cookie设置**
```php
// ❌ 不安全：普通Cookie
setcookie('session', $value);

// ✅ 安全：HTTPOnly + Secure
setcookie('session', $value, time()+3600, '/', '', true, true);
```

**效果：**
- JavaScript无法访问Cookie
- 仅HTTPS传输
- 防止Cookie窃取

## 💡 最佳实践

### 1. 多层防御
- 输入过滤 + 输出编码
- CSP策略 + Cookie安全
- 形成完整防御体系

### 2. 上下文感知
- 根据输出位置选择编码方式
- HTML → htmlspecialchars
- JS → json_encode
- URL → urlencode

### 3. 白名单过滤
- 允许特定标签和属性
- 比黑名单更安全
- 使用HTML Purifier等库

### 4. 定期审计
- 代码安全审计
- 自动化扫描工具
- 安全测试流程

## 🎓 学习建议

1. **理解原理** - 先了解XSS攻击原理
2. **实践防御** - 在防御演示中测试各种方法
3. **对比效果** - 观察安全与不安全代码的差异
4. **应用到项目** - 在实际项目中应用防御方法

## 📚 相关资源

- [OWASP XSS防御指南](https://owasp.org/www-community/attacks/xss/)
- [PHP安全最佳实践](https://www.php.net/manual/en/security.php)
- [CSP策略文档](https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP)

## ⚠️ 注意事项

- 防御方法需要根据场景选择
- 单一防御方法可能不够
- 需要组合多种防御策略
- 定期更新防御方法

---

**XSS防御演示模块帮助你构建安全的Web应用！** 🛡️