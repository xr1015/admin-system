<?php 
  // 载入配置文件
  require_once '../config.php';

 session_start();
 ?>
<?php 

function login () {
  // 1. 接收并校验
  // 表单的校验
  if (empty($_POST['email'])) {
    $GLOBALS['error_message'] = '请输入邮箱';
    return;
  }
  if (empty($_POST['password'])) {
    $GLOBALS['error_message'] = '请输入密码';
    return;
  }

  $email = $_POST['email'];
  $password = $_POST['password'];

  // 当客户端提交过来的完整的表单信息就应该对其进行数据校验
  $conn = mysqli_connect(XIU_DB_HOST, XIU_DB_USER, XIU_DB_PASS, XIU_DB_NAME);
  if (! $conn) {
    exit('<h1>数据库连接失败</h1>');
  }

  mysqli_set_charset($conn,"utf8");
  // 设置默认客户端字符集。数据库链接好后紧接着就设置字符集

  $query = mysqli_query($conn, "select * from users where email = '{$email}' limit 1;");

  if (!$query) {
    $GLOBALS['error_message'] = '登录失败，请重试！';
    return;
  }

  // 获取登录用户
  $user = mysqli_fetch_assoc($query);

  if (!$user) {
    // 用户名不存在
    $GLOBALS['error_message'] = '邮箱与密码不匹配';
    return;
  }

  // 一般密码是加密存储的
  if ($user['password'] !== $password) {
    // 密码不正确
    $GLOBALS['error_message'] = '邮箱与密码不匹配';
    return;
  }

  // 存一个登录标识
  // 为了后续可以直接获取当前登录用户的信息，这里直接将用户信息放到 session 中
  $_SESSION['current_login_user'] = $user;

  // 一切 OK
  header('Location: /admin/');
  // 2. 持久化
  
  // 3. 响应
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  login();
}

// 退出功能
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout') {
  // 删除了登录标识
  unset($_SESSION['current_login_user']);
}
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate.css">
</head>
<body>
  <div class="login">
    <!-- 可以通过在 form 上添加 novalidate 取消浏览器自带的校验功能 -->
    <!-- autocomplete="off" 关闭客户端的自动完成功能 -->
    <form class="login-wrap<?php echo isset($error_message) ? ' shake animated' : '' ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" novalidate autocomplete='off' >
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $error_message ?>
      </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <!-- 添加 value 属性 是为了实现 状态 的保持 使记住上次输入的邮箱值 -->
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus value="<?php echo empty($_POST['email']) ? '' : $_POST['email'] ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block" href="index.php">登 录</button>
    </form>
  </div>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script>
    $(function ($) {
      // 入口函数的作用
      // 1. 单独作用域
      // 2. 确保页面加载完成后执行
      

      // 因为数据在服务端，所以客户端要想取到数据，使用ajax技术
      // 判断是否是邮箱，最常见的技术手段是正则表达式
      // 目标：在用户输入自己的邮箱过后，页面上展示这个邮箱对应的头像
      // 实现：
      // - 时机： 邮箱文本框失去焦点，并且能够拿到文本框中填写的邮箱时
      // - 事情： 获取这个文本框中填写的邮箱对应的头像地址，展示到上面的 img 元素上
      
      var emailFormat = /^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/

      $('#email').on('blur',function () {
        var value = $(this).val()
        // 忽略掉文本框为空或者不是一个邮箱
        if (! value || ! emailFormat.test(value)) return
        
        // 用户输入了一个合理的邮箱地址
        // 获取这个文本框中填写的邮箱地址对应的头像地址，展示到上面的 img 元素上
        // 因为客户端的 JS 无法直接操作数据库，应该通过 JS 发送 AJAX 请求 告诉服务端的某个接口，让这个接口帮助客户端获取头像地址
        
        $.get('/admin/api/avatar.php',{email: value}, function (res) {
          // 希望 res => 这个邮箱对应的头像地址
          if (!res) return
          // 展示到上面的 img 元素上
          $('.avatar').fadeOut(function () {
            // 等到 淡出完成
            $(this).on('load',function () {
              // 图片完全加载成功过后
              $(this).fadeIn()
            }).attr('src',res)
          })
        })
      })
      
    })
  </script>
</body>
</html>
