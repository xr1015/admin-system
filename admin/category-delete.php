<?php 

/**
 * 根据客户端传递过来的 id 删除对应数据
 */

require_once '../function.php';

if (empty($_GET['id'])) {
	exit('缺少必要参数');
}

$id = $_GET['id'];

// $id = (int)$_GET['id'];
// => '1 or 1 = 1'
// sql 注入

xiu_execute('delete from categories where id in (' . $id . ');');

header('Location: /admin/categories.php');


 ?>