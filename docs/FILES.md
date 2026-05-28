# 📁 项目文件说明

## 根目录文件

| 文件名 | 说明 |
|--------|------|
| `README.md` | 项目主页说明 |
| `LICENSE` | MIT开源许可证 |
| `AUTHORS` | 作者信息 |
| `.gitignore` | Git忽略配置 |
| `.gitattributes` | Git属性配置 |
| `config.example.php` | 配置文件示例 |
| `demo.php` | 演示入口页面 |
| `xss_types_demo.php` | XSS类型演示（入门学习） |
| `forum.php` | XSS漏洞论坛页面 |
| `keylogger_demo.php` | 键盘记录演示页面 |
| `payload_library.php` | XSS Payload库页面 |
| `payload_library.js` | Payload库外部脚本 |
| `payloads.json` | Payload数据文件（235+条） |
| `defense_demo.php` | XSS防御演示页面 |
| `simple_test.html` | 简化测试页面 |
| `steal.php` | 凭据窃取处理脚本 |
| `steal_cookie.php` | Cookie窃取处理脚本 |
| `save_keylog.php` | 保存键盘记录脚本 |
| `viewer.php` | 整合数据查看器（凭据/Cookie/键盘记录） |
| `clear_credentials.php` | 清空凭据脚本 |
| `clear_cookies.php` | 清空Cookie脚本 |
| `clear_keylogs.php` | 清空键盘记录脚本 |
| `clear_forum_comments.php` | 清空论坛评论脚本 |
| `clear_xss_comments.php` | 清空XSS演示评论脚本 |

## 目录结构

### 📂 phishing/
钓鱼登录页面
- `login.html` - 伪造的登录页面

### 📂 data/
数据存储目录（自动生成）
- `stolen_credentials.txt` - 窃取的凭据数据
- `stolen_cookies.txt` - 窃取的Cookie数据
- `keylogs.txt` - 键盘记录数据
- `forum_comments.txt` - 论坛评论数据
- `xss_comments.txt` - XSS演示评论数据

### 📂 assets/
静态资源目录
- `css/` - 样式文件目录
- `js/` - JavaScript文件目录

### 📂 img/
截图目录
- `demo1.png`, `demo2.png`, `demo3.png` - 演示入口截图
- `forum.png` - 论坛页面截图
- `login.png` - 钓鱼登录页面截图
- `viewer.png` - 整合数据查看器截图
- `keylogger_demo.png` - 键盘记录演示截图
- `defense_demo1.png`, `defense_demo2.png` - 防御演示截图
- `payload_library1.png`, `payload_library2.png` - Payload库截图
- `test_payload.png` - Payload测试页面截图
- `xss_types_demo1.png`, `xss_types_demo2.png`, `xss_types_demo3.png` - XSS类型演示截图

### 📂 docs/
项目文档目录
- `README.md` - 详细项目说明
- `QUICKSTART.md` - 快速开始指南
- `CHANGELOG.md` - 更新日志
- `SECURITY.md` - 安全政策
- `CONTRIBUTING.md` - 贡献指南
- `CODE_STYLE.md` - 代码规范
- `STRUCTURE.md` - 项目结构
- `BADGES.md` - 项目徽章
- `CHECKLIST.md` - GitHub上传检查清单
- `PROJECT_SUMMARY.md` - 项目总结
- `DEPLOYMENT.md` - 部署指南
- `SCREENSHOTS.md` - 截图文档

## 快速开始

1. 将项目部署到PHP服务器
2. 访问 `demo.php` 开始演示
3. 查看 `docs/` 目录获取详细文档

## 注意事项

⚠️ 本项目仅用于教育和研究目的，请勿用于非法用途！