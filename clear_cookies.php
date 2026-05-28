<?php
/**
 * 清空窃取的Cookie数据
 * 
 * @author  Guojin0826
 * @email   jinrcsy@gmail.com
 * @github  https://github.com/Guojin0826
 */

header('Content-Type: application/json');

$log_file = 'data/stolen_cookies.txt';
if(file_exists($log_file)) {
    file_put_contents($log_file, '');
    echo json_encode(['status' => 'success', 'message' => 'Cookie数据已清空']);
} else {
    echo json_encode(['status' => 'success', 'message' => 'Cookie数据已清空']);
}
exit;
?>