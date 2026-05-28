# 更新日志

所有重要的更改都将记录在此文件中。

格式基于 [Keep a Changelog](https://keepachangelog.com/zh-CN/1.0.0/)，
并且本项目遵循 [语义化版本](https://semver.org/lang/zh-CN/)。

## [1.2.0] - 2026-05-28

### 新增
- ✨ Payload库大幅扩充
  - 收录235+个XSS攻击向量
  - 从Payload.txt导入大量Payload
  - 新增多个分类：JavaScript URL、HTML事件属性、Script标签、IMG标签、iframe标签、SVG标签、CSS样式注入、高级绕过技术
- ✨ Payload测试功能
  - 新增test_payload.html测试页面
  - 新增simple_test.html简化测试页面
  - 支持手动输入Payload测试
  - 支持从URL参数加载Payload
  - 智能处理script标签执行
- ✨ Payload库外部脚本
  - 新增payload_library.js外部脚本文件
  - 绕过CSP策略限制
  - 优化代码组织结构
- 📝 新增Payload触发方式智能提示
- 📝 新增Payload构成解释

### 修复
- 🐛 修复JSON格式错误（非法转义字符\j、\v、\s等）
- 🐛 修复CSP策略阻止字体加载问题
- 🐛 修复正则表达式转义错误（\\s应为\s）
- 🐛 修复innerHTML插入script标签不执行的问题
- 🐛 修复测试页面一直加载中的问题

### 更新
- 📝 更新所有文档，匹配当前项目状态
- 📝 更新文件结构说明
- 📝 更新Payload库文档
- 🎨 优化代码格式化

## [1.1.1] - 2026-05-27

### 修复
- 🐛 修复payload_library.php页面的XSS漏洞
  - 添加escapeHtml函数对所有动态内容进行HTML转义
  - 修复renderPayloadCard中所有字段的转义（name、code、desc、tags）
  - 修复testPayload中的内容转义
  - 确保Payload以纯文本形式展示，不会自动执行
- 📝 新增XSS漏洞修复说明文档（docs/XSS_FIX.md）
- 🧪 新增XSS测试示例页面（xss_test.html）

## [1.1.0] - 2026-05-26

### 新增
- ✨ XSS Payload库功能（payload_library.php）
  - 收录80+个常见XSS攻击向量
  - 6大分类：基础、绕过过滤、事件处理、特殊场景、高级技巧、编码绕过
  - 一键复制Payload功能
  - 危害等级标识（低/中/高/严重）
  - Payload效果说明
  - 测试功能（跳转到论坛测试）
- ✨ XSS防御演示模块（defense_demo.php）
  - 5种防御方法对比
  - 实时演示效果
  - 防御代码示例
- 📝 Payload库文档（docs/PAYLOAD_LIBRARY.md）
- 📝 防御演示文档（docs/DEFENSE_DEMO.md）

### 更新
- 🎨 优化demo.php模块布局和排序
- 🎨 改进UI界面设计
- 📝 更新README.md，添加Payload库说明
- 📝 更新项目结构文档

## [1.0.0] - 2024-01-15

### 新增
- ✨ XSS漏洞演示靶场初始版本
- ✨ 拟真技术论坛页面（forum.php）
- ✨ 钓鱼攻击演示功能
  - 伪造登录页面
  - 凭据窃取与存储
  - 自动跳转机制
- ✨ Cookie窃取演示功能
  - 多种Payload实现方式
  - 实时数据捕获
  - 数据查看界面
- ✨ 演示控制面板
  - Payload选择器
  - 一键注入功能
  - 触发按钮
- ✨ XSS Payload构成解析
  - 详细代码注释
  - 组件拆解说明
  - 技术要点讲解
- ✨ 数据管理功能
  - 凭据查看器
  - Cookie查看器
  - 数据清空功能
- ✨ 完整文档
  - README.md
  - QUICKSTART.md
  - CONTRIBUTING.md
  - SECURITY.md

### 安全
- 🔒 添加MIT许可证
- 🔒 添加安全警告说明
- 🔒 仅限本地测试使用
- 🔒 数据目录权限控制

### 文档
- 📝 完整的项目文档
- 📝 快速启动指南
- 📝 贡献指南
- 📝 安全政策

### 优化
- 🎨 统一的UI设计风格
- 🎨 响应式布局
- 🎨 代码格式化
- 🎨 目录结构优化

## [未发布]

### 计划新增
- 🔮 DOM型XSS演示
- 🔮 存储型XSS演示
- 🔮 反射型XSS演示
- 🔮 XSS Filter绕过演示
- 🔮 CSP策略演示
- 🔮 防御措施演示

---

## 版本说明

- **[1.0.0]** - 初始发布版本，包含核心功能
- **[未发布]** - 计划中的功能，将在未来版本中实现