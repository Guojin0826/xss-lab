// XSS Payload库 - JavaScript代码

// 全局变量
let allPayloads = [];
let currentFilter = 'all';

// 页面加载完成后初始化
window.addEventListener('DOMContentLoaded', function() {
    console.log('页面加载完成，开始初始化...');
    loadPayloads();
});

// 从JSON文件加载payload数据
async function loadPayloads() {
    try {
        console.log('正在加载payload数据...');
        const response = await fetch('./payloads.json');
        if (!response.ok) {
            throw new Error('加载失败: ' + response.status);
        }
        allPayloads = await response.json();
        console.log('加载成功，共 ' + allPayloads.length + ' 个payload');
        
        renderPayloads(allPayloads);
        updateStats(allPayloads);
        bindEvents();
        
        console.log('初始化完成');
    } catch (error) {
        console.error('加载payload数据失败:', error);
        document.getElementById('payloadsContainer').innerHTML = 
            '<div class="no-results"><i class="fas fa-exclamation-circle"></i><p>加载数据失败，请刷新页面重试</p></div>';
    }
}

// 渲染payload列表
function renderPayloads(payloads) {
    const container = document.getElementById('payloadsContainer');
    container.innerHTML = '';

    if (payloads.length === 0) {
        container.innerHTML = '<div class="no-results"><i class="fas fa-search"></i><p>没有找到匹配的Payload</p></div>';
        return;
    }

    payloads.forEach((payload, index) => {
        const card = document.createElement('div');
        card.className = 'payload-card';

        // 创建头部
        const header = document.createElement('div');
        header.className = 'payload-header';

        const name = document.createElement('div');
        name.className = 'payload-name';
        name.textContent = payload.name;

        const level = document.createElement('span');
        level.className = 'payload-level level-' + payload.level;
        level.textContent = getLevelText(payload.level);

        header.appendChild(name);
        header.appendChild(level);

        // 创建描述
        const desc = document.createElement('div');
        desc.className = 'payload-desc';
        desc.textContent = payload.desc;

        // 创建代码块
        const codeBlock = document.createElement('div');
        codeBlock.className = 'payload-code';

        const code = document.createElement('code');
        code.textContent = payload.code;

        const copyBtn = document.createElement('button');
        copyBtn.className = 'copy-btn';
        copyBtn.textContent = '复制';
        copyBtn.addEventListener('click', function() {
            copyToClipboard(payload.code);
        });

        codeBlock.appendChild(code);
        codeBlock.appendChild(copyBtn);

        // 创建标签
        const tags = document.createElement('div');
        tags.className = 'payload-tags';
        payload.tags.forEach(tag => {
            const tagElement = document.createElement('span');
            tagElement.className = 'tag';
            tagElement.textContent = tag;
            tags.appendChild(tagElement);
        });

        // 创建触发说明
        const trigger = analyzeTrigger(payload.code);
        const triggerInfo = document.createElement('div');
        triggerInfo.className = 'trigger-info';
        triggerInfo.innerHTML = `
            <div class="trigger-badge" style="background: ${trigger.color};">
                <span class="trigger-icon">${trigger.icon}</span>
                <span class="trigger-text">${trigger.text}</span>
            </div>
            <div class="trigger-desc">${trigger.desc}</div>
        `;

        // 创建操作按钮
        const actions = document.createElement('div');
        actions.className = 'payload-actions';

        const testBtn = document.createElement('button');
        testBtn.className = 'test-btn';
        testBtn.innerHTML = '<i class="fas fa-flask"></i> 测试';
        testBtn.addEventListener('click', function() {
            testPayload(payload.code, payload.name);
        });

        actions.appendChild(testBtn);

        // 组装卡片
        card.appendChild(header);
        card.appendChild(desc);
        card.appendChild(triggerInfo);
        card.appendChild(codeBlock);
        card.appendChild(tags);
        card.appendChild(actions);

        container.appendChild(card);
    });
}

// 获取危险等级文本
function getLevelText(level) {
    const levelMap = {
        'low': '低危',
        'medium': '中危',
        'high': '高危',
        'critical': '严重'
    };
    return levelMap[level] || level;
}

// 分析Payload触发方式
function analyzeTrigger(code) {
    const codeLower = code.toLowerCase();
    
    // 自动触发（页面加载时立即执行）
    if (codeLower.includes('<script') || 
        codeLower.includes('onerror=') || 
        codeLower.includes('onload=') ||
        codeLower.includes('<svg') && codeLower.includes('onload') ||
        codeLower.includes('<body') && codeLower.includes('onload') ||
        codeLower.includes('<iframe') && codeLower.includes('onload') ||
        codeLower.includes('<img') && codeLower.includes('onerror')) {
        return {
            type: 'auto',
            text: '自动触发',
            icon: '⚡',
            desc: '页面加载时自动执行，无需用户交互',
            color: '#e74c3c'
        };
    }
    
    // 需要点击触发
    if (codeLower.includes('onclick=') || 
        codeLower.includes('ondblclick=') ||
        codeLower.includes('<a') && codeLower.includes('javascript:') ||
        codeLower.includes('<button') ||
        codeLower.includes('<details') ||
        codeLower.includes('onmousedown=') ||
        codeLower.includes('onmouseup=')) {
        return {
            type: 'click',
            text: '点击触发',
            icon: '👆',
            desc: '需要用户点击特定元素才能触发',
            color: '#f39c12'
        };
    }
    
    // 需要鼠标悬停
    if (codeLower.includes('onmouseover=') || 
        codeLower.includes('onmouseenter=') ||
        codeLower.includes('onmousemove=')) {
        return {
            type: 'hover',
            text: '悬停触发',
            icon: '🖱️',
            desc: '需要鼠标悬停在元素上才能触发',
            color: '#3498db'
        };
    }
    
    // 需要焦点
    if (codeLower.includes('onfocus=') || 
        codeLower.includes('onblur=') ||
        codeLower.includes('<input') ||
        codeLower.includes('<textarea')) {
        return {
            type: 'focus',
            text: '焦点触发',
            icon: '🎯',
            desc: '需要元素获得或失去焦点时触发',
            color: '#9b59b6'
        };
    }
    
    // 需要键盘交互
    if (codeLower.includes('onkeydown=') || 
        codeLower.includes('onkeyup=') ||
        codeLower.includes('onkeypress=')) {
        return {
            type: 'keyboard',
            text: '键盘触发',
            icon: '⌨️',
            desc: '需要键盘按键操作才能触发',
            color: '#1abc9c'
        };
    }
    
    // 需要表单提交
    if (codeLower.includes('onsubmit=') || 
        codeLower.includes('onchange=') ||
        codeLower.includes('oninput=')) {
        return {
            type: 'form',
            text: '表单触发',
            icon: '📝',
            desc: '需要表单操作（输入、提交等）才能触发',
            color: '#e67e22'
        };
    }
    
    // 需要拖拽
    if (codeLower.includes('ondrag') || 
        codeLower.includes('ondrop')) {
        return {
            type: 'drag',
            text: '拖拽触发',
            icon: '抓手',
            desc: '需要拖拽操作才能触发',
            color: '#95a5a6'
        };
    }
    
    // 默认：需要特定条件
    return {
        type: 'special',
        text: '特殊触发',
        icon: '🔧',
        desc: '需要特定条件或组合操作才能触发',
        color: '#7f8c8d'
    };
}

// 更新统计数据
function updateStats(payloads) {
    const stats = {
        total: payloads.length,
        low: 0,
        medium: 0,
        high: 0,
        critical: 0
    };

    payloads.forEach(payload => {
        stats[payload.level]++;
    });

    document.getElementById('totalCount').textContent = stats.total;
    document.getElementById('lowCount').textContent = stats.low;
    document.getElementById('mediumCount').textContent = stats.medium;
    document.getElementById('highCount').textContent = stats.high;
    document.getElementById('criticalCount').textContent = stats.critical;
}

// 绑定事件
function bindEvents() {
    // 搜索事件
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', function() {
        filterPayloads();
    });

    // 过滤按钮事件
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.getAttribute('data-filter');
            filterPayloads();
        });
    });
}

// 过滤payload
function filterPayloads() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    let filtered = allPayloads;

    // 按分类过滤
    if (currentFilter !== 'all') {
        filtered = filtered.filter(payload => payload.category === currentFilter);
    }

    // 按搜索词过滤
    if (searchTerm) {
        filtered = filtered.filter(payload => 
            payload.name.toLowerCase().includes(searchTerm) ||
            payload.desc.toLowerCase().includes(searchTerm) ||
            payload.tags.some(tag => tag.toLowerCase().includes(searchTerm))
        );
    }

    renderPayloads(filtered);
    updateStats(filtered);
}

// 复制到剪贴板
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('已复制到剪贴板');
    }).catch(err => {
        // 降级方案
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        showToast('已复制到剪贴板');
    });
}

// 测试Payload - 统一使用新窗口测试
function testPayload(code, name) {
    console.log('=== 开始测试Payload ===');
    console.log('Payload名称:', name);
    console.log('Payload代码:', code);
    
    try {
        // 使用Base64编码传递Payload数据
        const payloadData = { name: name, code: code };
        const encodedData = btoa(encodeURIComponent(JSON.stringify(payloadData)));
        const testUrl = 'test_payload.html?data=' + encodedData;
        
        // 尝试打开新窗口
        const testWindow = window.open(testUrl, '_blank', 'width=1000,height=800,scrollbars=yes,resizable=yes');
        
        if (testWindow) {
            console.log('✅ 新窗口已打开');
            showToast('✅ 已在新窗口打开测试环境');
            return;
        }
    } catch (error) {
        console.error('打开新窗口失败:', error);
    }
    
    // 如果新窗口被阻止，提供选择
    const choice = confirm(
        '弹出窗口被阻止！\n\n' +
        '请允许浏览器弹出窗口，或：\n' +
        '点击【确定】在新标签页打开测试\n' +
        '点击【取消】跳转到简单测试页面\n\n' +
        '提示：请在浏览器地址栏右侧允许弹出窗口'
    );
    
    if (choice) {
        // 在新标签页打开
        const payloadData = { name: name, code: code };
        const encodedData = btoa(encodeURIComponent(JSON.stringify(payloadData)));
        const testUrl = 'test_payload.html?data=' + encodedData;
        window.open(testUrl, '_blank');
    } else {
        // 跳转到简单测试页面（使用Base64编码）
        const encodedPayload = btoa(encodeURIComponent(code));
        const simpleUrl = 'simple_test.html?test=' + encodedPayload;
        window.location.href = simpleUrl;
    }
}

// 在当前页面显示测试结果（替代方案）
function showTestInCurrentPage(code, name) {
    // 创建模态框
    const modal = document.createElement('div');
    modal.id = 'testModal';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
    `;
    
    const content = document.createElement('div');
    content.style.cssText = `
        background: white;
        padding: 30px;
        border-radius: 15px;
        max-width: 900px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        position: relative;
    `;
    
    content.innerHTML = `
        <button onclick="document.getElementById('testModal').remove()" style="position: absolute; top: 15px; right: 15px; background: #ff5252; color: white; border: none; padding: 8px 16px; border-radius: 20px; cursor: pointer; font-size: 14px;">✕ 关闭</button>
        <h2 style="margin-bottom: 20px; color: #333;">🧪 测试: ${name}</h2>
        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">⚠️ 此区域用于安全测试，Payload将在下方执行</div>
        <h3 style="color: #667eea; margin: 20px 0 10px 0;">📝 Payload 源代码：</h3>
        <div style="background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 8px; font-family: monospace; white-space: pre-wrap; word-break: break-all; margin-bottom: 20px;" id="codeDisplay"></div>
        <h3 style="color: #667eea; margin: 20px 0 10px 0;">🎯 渲染执行区域：</h3>
        <div style="border: 2px dashed #667eea; padding: 20px; min-height: 100px; background: #fafafa; border-radius: 8px;" id="renderArea"></div>
    `;
    
    modal.appendChild(content);
    document.body.appendChild(modal);
    
    // 设置代码显示（使用textContent防止执行）
    document.getElementById('codeDisplay').textContent = code;
    
    // 在渲染区域执行payload
    document.getElementById('renderArea').innerHTML = code;
    
    // 点击背景关闭
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// 显示Toast提示
function showToast(message) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    toastMessage.textContent = message;
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 2000);
}