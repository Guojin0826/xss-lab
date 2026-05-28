<?php
/**
 * 清空XSS演示评论数据
 */

$data_file = __DIR__ . '/data/xss_demo_comments.txt';

if (file_exists($data_file)) {
    file_put_contents($data_file, '');
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => '评论已清空']);
