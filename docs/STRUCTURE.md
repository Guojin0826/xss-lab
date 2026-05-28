# 项目结构

```
xss-lab/
│
├── 📁 data/                          # 数据存储目录
│   ├── 📄 stolen_credentials.txt      # 窃取的凭据数据
│   ├── 📄 stolen_cookies.txt          # 窃取的Cookie数据
│   ├── 📄 forum_comments.txt          # 论坛评论数据
│   └── 📄 .gitkeep                    # 保持目录结构
│
├── 📁 phishing/                       # 钓鱼攻击模块
│   └── 📄 login.html                  # 伪造的登录页面
│
├── 📁 assets/                         # 静态资源目录
│   ├── 📁 css/                        # 样式文件
│   │   └── 📄 .gitkeep
│   └── 📁 js/                         # 脚本文件
│       └── 📄 .gitkeep
│
├── 📁 img/                            # 截图目录
│
└── 📄 .gitkeep
│
├── 📄 demo.php                        # 🏠 演示首页
├── 📄 forum.php                       # 💬 论坛页面（XSS漏洞场景）
├── 📄 payload_library.php             # 💣 XSS Payload库
├── 📄 payload_library.js              # 📜 Payload库外部脚本
├── 📄 payloads.json                   # 📋 Payload数据（235+条）
├── 📄 defense_demo.php                # 🛡️ XSS防御演示
├── 📄 test_payload.html               # 🧪 Payload测试页面
├── 📄 simple_test.html                # ⚡ 简化测试页面
│
├── 📄 steal.php                       # 🔓 接收窃取的凭据
├── 📄 steal_cookie.php                # 🔓 接收窃取的Cookie
│
├── 📄 view_credentials.php            # 👁️ 查看窃取的凭据
├── 📄 view_cookies.php                # 👁️ 查看窃取的Cookie
│
├── 📄 clear_credentials.php           # 🗑️ 清空凭据数据
├── 📄 clear_cookies.php               # 🗑️ 清空Cookie数据
├── 📄 clear_forum_comments.php        # 🗑️ 清空论坛评论
│
├── 📄 config.example.php              # ⚙️ 配置文件示例
│
├── 📄 README.md                       # 📖 项目说明文档
├── 📄 LICENSE                         # 📜 MIT许可证
├── 📄 AUTHORS                         # 👥 作者信息
├── 📄 .gitignore                      # 🚫 Git忽略文件
│
└── 📁 docs/                           # 📚 详细文档目录
```

## 📊 文件统计

| 类型 | 数量 | 说明 |
|------|------|------|
| PHP文件 | 11个 | 核心功能实现 |
| HTML文件 | 3个 | 测试页面和钓鱼页面 |
| JavaScript文件 | 1个 | Payload库脚本 |
| JSON文件 | 1个 | Payload数据 |
| Markdown文件 | 20+个 | 文档说明 |
| 配置文件 | 3个 | 项目配置 |
| **总计** | **40+** | 完整项目 |

## 🎯 核心文件说明

### 主要功能文件

| 文件 | 功能 | 重要性 |
|------|------|--------|
| `demo.php` | 演示首页，项目导航 | ⭐⭐⭐⭐⭐ |
| `forum.php` | XSS漏洞演示场景 | ⭐⭐⭐⭐⭐ |
| `payload_library.php` | XSS Payload库 | ⭐⭐⭐⭐⭐ |
| `payloads.json` | Payload数据存储 | ⭐⭐⭐⭐⭐ |
| `defense_demo.php` | XSS防御演示 | ⭐⭐⭐⭐ |
| `phishing/login.html` | 钓鱼登录页面 | ⭐⭐⭐⭐ |
| `steal.php` | 凭据接收脚本 | ⭐⭐⭐⭐ |
| `steal_cookie.php` | Cookie接收脚本 | ⭐⭐⭐⭐ |
| `view_credentials.php` | 凭据查看器 | ⭐⭐⭐ |
| `view_cookies.php` | Cookie查看器 | ⭐⭐⭐ |

### 文档文件

| 文件 | 内容 | 读者 |
|------|------|------|
| `README.md` | 项目完整说明 | 所有用户 |
| `QUICKSTART.md` | 快速启动指南 | 新用户 |
| `CHANGELOG.md` | 版本更新记录 | 开发者 |
| `CODE_STYLE.md` | 代码规范 | 贡献者 |
| `CONTRIBUTING.md` | 贡献指南 | 贡献者 |
| `SECURITY.md` | 安全政策 | 所有用户 |

### 配置文件

| 文件 | 用途 |
|------|------|
| `config.example.php` | 配置模板 |
| `project.json` | 项目元数据 |
| `.gitignore` | Git版本控制 |
| `LICENSE` | 开源许可证 |

## 🔄 数据流向

```
用户访问 forum.php
        ↓
注入XSS Payload
        ↓
触发恶意代码
        ↓
    ┌───────────┴───────────┐
    ↓                       ↓
钓鱼攻击              Cookie窃取
    ↓                       ↓
phishing/login.html    steal_cookie.php
    ↓                       ↓
steal.php              data/stolen_cookies.txt
    ↓
data/stolen_credentials.txt
    ↓
view_credentials.php   view_cookies.php
```

## 🚀 部署流程

```
1. 克隆项目
   git clone https://github.com/user/xss-lab.git

2. 配置环境
   cp config.example.php config.php
   
3. 设置权限
   chmod -R 755 .
   chmod -R 777 data/

4. 启动服务
   php -S localhost:8000

5. 访问演示
   http://localhost:8000/demo.php
```

---

**清晰的项目结构，便于理解和维护！**