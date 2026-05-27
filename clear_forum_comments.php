<?php
/**
 * 清理论坛留言
 * @author  Guojin0826
 * @email   jinrcsy@gmail.com
 */

header('Content-Type: application/json');

$commentFile = 'forum_comments.txt';

try {
    // 清空留言文件（写入空数组的JSON）
    file_put_contents($commentFile, json_encode([]));
    echo json_encode([
        'success' => true,
        'message' => '论坛留言已清空！'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => '清理失败：' . $e->getMessage()
    ]);
}
?>
