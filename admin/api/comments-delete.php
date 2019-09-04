<?php 

/**
 * 根据客户端传递过来的 id 删除对应数据
 */

require_once '../../function.php';

if (empty($_GET['id'])) {
	exit('缺少必要参数');
}

$id = $_GET['id'];

// $id = (int)$_GET['id'];
// => '1 or 1 = 1'
// sql 注入

$rows = xiu_execute('delete from comments where id in (' . $id . ');');

header('Content-Type: application/json');

echo json_encode($rows > 0);
// 'true' | 'false'

