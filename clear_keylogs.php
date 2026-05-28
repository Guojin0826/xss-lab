<?php
/**
 * 清空键盘记录数据
 * 用于重置演示环境
 */

header('Content-Type: application/json');

$keylogFile = __DIR__ . '/data/keylog.txt';

try {
    // 清空键盘记录文件
    if (file_exists($keylogFile)) {
        file_put_contents($keylogFile, '');
    }
    
    echo json_encode([
        'success' => true,
        'message' => '键盘记录已清空'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => '清空失败：' . $e->getMessage()
    ]);
}
?>
