# 图片资源说明

本目录包含XSS Lab项目的所有截图和图片资源。

---

## 📸 图片列表

| 文件名 | 用途 | 尺寸建议 |
|--------|------|----------|
| demo1.png | 演示入口页面截图 | 1920x1080 |
| demo2.png | 演示控制面板截图 | 1920x1080 |
| demo3.png | 攻击流程演示截图 | 1920x1080 |
| forum.png | 论坛主页面截图 | 1920x1080 |
| login.png | 钓鱼登录页面截图 | 1920x1080 |
| view_credentials.png | 凭据查看页面截图 | 1920x1080 |
| view_cookies.png | Cookie查看页面截图 | 1920x1080 |
| defense_demo1.png | XSS防御演示页面截图1 | 1920x1080 |
| defense_demo2.png | XSS防御演示页面截图2 | 1920x1080 |
| payload_library1.png | XSS Payload库页面截图1 | 1920x1080 |
|| payload_library2.png | XSS Payload库页面截图2 | 1920x1080 |
|| test_payload.png | Payload测试页面截图 | 1920x1080 |
| phishing_demo.png | 钓鱼平台功能演示截图 | 1920x1080 |

---

## 🎯 使用说明

### 在Markdown中引用图片

```markdown
![演示入口](img/demo1.png)
![论坛页面](img/forum.png)
![钓鱼页面](img/login.png)
```

### 在HTML中引用图片

```html
<img src="img/demo1.png" alt="演示入口" width="800"/>
```

### 在PHP中引用图片

```php
<img src="img/demo1.png" alt="演示入口" class="img-fluid">
```

---

## 📐 截图规范

### 推荐尺寸
- **宽度：** 1920px 或 1280px
- **高度：** 1080px 或 720px
- **格式：** PNG（推荐）或 JPG
- **质量：** 高清，文件大小 < 2MB

### 截图要求
- ✅ 清晰可读
- ✅ 完整展示功能
- ✅ 无敏感信息
- ✅ 统一浏览器窗口大小
- ✅ 使用无痕模式（避免插件干扰）

---

## 🔄 更新截图

如需更新截图，请按照以下步骤：

1. **准备环境**
   ```bash
   # 启动本地服务器
   php -S localhost:8000
   ```

2. **打开浏览器**
   - 使用Chrome/Firefox
   - 设置窗口大小为1920x1080
   - 使用无痕模式

3. **截取屏幕**
   - Windows: `Win + Shift + S`
   - Mac: `Cmd + Shift + 4`
   - Chrome: `F12` -> `Ctrl + Shift + P` -> 输入 `screenshot`

4. **保存图片**
   - 保存到 `img/` 目录
   - 使用有意义的文件名
   - 确保格式为PNG

---

## 📝 图片优化

### 压缩图片（可选）

```bash
# 使用pngquant压缩PNG
pngquant --quality=65-80 img/*.png

# 使用optipng优化PNG
optipng -o7 img/*.png
```

### 添加水印（可选）

如需添加水印，可使用ImageMagick：

```bash
composite -dissolve 30% -gravity southeast watermark.png demo1.png demo1.png
```

---

## ⚠️ 注意事项

- ❌ 不要包含真实的敏感信息
- ❌ 不要包含真实的用户数据
- ❌ 不要包含真实的IP地址
- ✅ 使用示例数据
- ✅ 使用虚构的用户名
- ✅ 使用虚构的凭据

---

**最后更新：** 2024年
