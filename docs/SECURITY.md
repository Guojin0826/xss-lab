# 安全政策 / Security Policy

## 🔒 支持的版本

| 版本 | 支持状态 |
| --- | --- |
| 1.0.x | ✅ 支持 |

## ⚠️ 重要声明

**本项目是一个XSS漏洞演示靶场，故意包含安全漏洞！**

### 项目性质
- 本项目**故意**包含XSS（跨站脚本攻击）漏洞
- 这些漏洞是为了教学和演示目的而设计的
- **请勿在生产环境中部署本项目**

### 安全建议
1. 仅在隔离的测试环境中运行
2. 不要暴露到公网
3. 不要使用真实的用户数据
4. 定期清理测试数据

## 📢 报告安全漏洞

虽然本项目故意包含XSS漏洞，但如果您发现了：

### 应该报告的问题
- 非预期的安全漏洞
- 可能导致服务器被完全控制的问题
- 意外的信息泄露
- 其他非教学目的的安全问题

### 如何报告

请通过以下方式报告：

1. **私密报告（推荐）**
   - 使用GitHub的[Security Advisories](https://github.com/Guojin0826/xss-lab/security/advisories)功能
   - 这允许私密地讨论和修复问题

2. **邮件报告**
   - 发送邮件至：jinrcsy@gmail.com
   - 标题：[SECURITY] XSS Demo Security Issue
   - 包含：问题描述、复现步骤、潜在影响

### 报告内容应包括

- 问题的详细描述
- 复现步骤
- 潜在的安全影响
- 如果可能，提供修复建议
- 您的联系方式（用于后续沟通）

## 🎯 响应流程

1. **确认收到** - 我们会在48小时内确认收到您的报告
2. **初步评估** - 评估问题的严重性和影响范围
3. **调查分析** - 详细调查问题原因
4. **制定修复** - 开发修复方案
5. **测试验证** - 确保修复有效且不引入新问题
6. **发布更新** - 发布修复版本并通知

## 🏆 致谢

我们感谢所有负责任地披露安全问题的研究者。

在获得您的许可后，我们会在以下位置致谢：
- 发布说明
- SECURITY.md文件

## 📚 安全最佳实践

如果您想学习如何防御XSS攻击，请参考：

### 输入验证
```php
// 永远不要信任用户输入
$clean_input = htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');
```

### 输出编码
```php
// 根据上下文选择合适的编码方式
echo htmlentities($data, ENT_QUOTES, 'UTF-8');
```

### 内容安全策略（CSP）
```php
// 限制脚本来源
header("Content-Security-Policy: default-src 'self'");
```

### Cookie安全
```php
// 设置HttpOnly和Secure标志
setcookie($name, $value, [
    'httponly' => true,
    'secure' => true,
    'samesite' => 'Strict'
]);
```

## 📖 学习资源

- [OWASP XSS Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html)
- [MDN Web Security](https://developer.mozilla.org/en-US/docs/Web/Security)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)

---

**记住：安全是每个人的责任！**
