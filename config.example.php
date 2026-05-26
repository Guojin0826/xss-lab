<?php
/**
 * 配置文件示例
 * 
 * @author  Guojin0826
 * @email   jinrcsy@gmail.com
 * @github  https://github.com/Guojin0826
 * 
 * 请复制此文件为 config.php 并修改配置
 * cp config.example.php config.php
 */

// 数据目录配置
define('DATA_DIR', __DIR__ . '/data');

// 日志文件配置
define('CREDENTIALS_FILE', DATA_DIR . '/stolen_credentials.txt');
define('COOKIES_FILE', DATA_DIR . '/stolen_cookies.txt');

// 安全配置
define('ENABLE_XSS_PROTECTION', false); // 演示环境关闭XSS防护
define('ALLOWED_IPS', ['127.0.0.1', '::1']); // 允许访问的IP

// 调试配置
define('DEBUG_MODE', true);
define('LOG_ERRORS', true);
define('ERROR_LOG', DATA_DIR . '/errors.log');

// 时区设置
date_default_timezone_set('Asia/Shanghai');

/**
 * 检查IP是否允许访问
 * 
 * @return bool
 */
function isAllowedIP() {
    $clientIP = $_SERVER['REMOTE_ADDR'] ?? '';
    return in_array($clientIP, ALLOWED_IPS) || empty(ALLOWED_IPS);
}

/**
 * 安全输出HTML
 * 
 * @param string $content 要输出的内容
 * @return string 转义后的内容
 */
function safeOutput($content) {
    return htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
}

/**
 * 记录错误日志
 * 
 * @param string $message 错误信息
 */
function logError($message) {
    if (LOG_ERRORS) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$message}\n";
        file_put_contents(ERROR_LOG, $logMessage, FILE_APPEND);
    }
}

// 检查数据目录
if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
}

// 检查文件权限
if (!is_writable(DATA_DIR)) {
    die('错误：数据目录不可写，请执行: chmod 777 ' . DATA_DIR);
}
?>