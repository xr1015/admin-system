<?php 

// 校验数据当前访问用户的 箱子（session）有没有登录的登录标识
require_once '../function.php';

// 判断用户是否登录一定是最先去做
xiu_get_current_user();

// 获取界面所需要的数据
// 重复的操作一定封装起来 
$posts_count = xiu_fetch_one('select count(1) as num from posts;');

$posts_count_status = xiu_fetch_one("select count(1) as num from posts where status = 'drafted';");

$comments_count = xiu_fetch_one('select count(1) as num from comments;');


$comments_count_status = xiu_fetch_one("select count(1) as num from comments where status = 'held';");

$categories_count = xiu_fetch_one('select count(1) as num from categories;');

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">

   <?php include 'nav/navbar.php'; ?>

    <div class="container-fluid">
      <div class="jumbotron text-center">
        <h1>One Belt, One Road</h1>
        <p>Thoughts, stories and ideas.</p>
        <p><a class="btn btn-primary btn-lg" href="post-add.html" role="button">写文章</a></p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">站点内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item"><strong><?php echo $posts_count['num']; ?></strong>篇文章（<strong><?php echo $posts_count_status['num'] ?></strong>篇草稿）</li>
              <li class="list-group-item"><strong><?php echo $categories_count['num']; ?></strong>个分类</li>
              <li class="list-group-item"><strong><?php echo $comments_count['num']; ?></strong>条评论（<strong><?php echo $comments_count_status['num'] ?></strong>条待审核）</li>
            </ul>
          </div>
        </div>
        <div class="col-md-4">
          <canvas id="chart"></canvas>
        </div>

        <div class="col-md-4"></div>
      </div>
    </div>
  </div>
 <?php $current_page  = 'index'; ?>
  <!-- 使用 include 载入 而不使用 require 的原因是，就是被载入文件不存在或出错，后面的代码也会正常输出 -->
  <?php include 'inc/sidebar.php'; ?>
  

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/chart/chart.js"></script>
  <script>
    var ctx = document.getElementById('chart').getContext('2d');
    var myChart = new Chart(ctx, {
    type: 'pie',
    data: {
      datasets: [
        // datasets 中的一个集合就是一张饼图
        {
          data: [<?php echo $posts_count['num']; ?>, <?php echo $categories_count['num']; ?>, <?php echo $comments_count['num']; ?>],
          backgroundColor:[
            '#607B8B',
            '#4F94CD',
            '#20B2AA',
          ]    
        },
        {
          data: [<?php echo $posts_count['num']; ?>, <?php echo $categories_count['num']; ?>, <?php echo $comments_count['num']; ?>],
          backgroundColor:[
            '#607B8B',
            '#4F94CD',
            '#20B2AA',
          ]    
        }

      ],
      labels: [
          '文章',
          '分类',
          '评论'
      ],

  }
});
</script>
  <script>NProgress.done()</script>
</body>
</html>
