<?php
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function csrf_token(){ if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function csrf_check(){ if($_SERVER['REQUEST_METHOD']==='POST'){ $t=$_POST['csrf']??''; if(!is_string($t)||!hash_equals($_SESSION['csrf']??'', $t)){ http_response_code(403); exit('Security token expired. Please go back and try again.'); } } }
function flash($msg=null){ if($msg!==null){$_SESSION['flash']=$msg;return;} $m=$_SESSION['flash']??'';unset($_SESSION['flash']);return $m; }
function require_login(){ if(empty($_SESSION['user'])){header('Location: index.php');exit;} if(time()-(int)($_SESSION['last_activity']??time())>1800){session_unset();session_destroy();header('Location: index.php?timeout=1');exit;} $_SESSION['last_activity']=time(); }
function require_student(){ global $pdo; require_login(); if(($_SESSION['user']['role']??'')!=='student'){header('Location: admin.php');exit;} $q=$pdo->prepare("SELECT u.status AS user_status,s.status AS student_status FROM users u LEFT JOIN students s ON s.user_id=u.id WHERE u.id=? AND u.role='student'"); $q->execute([(int)$_SESSION['user']['id']]); $state=$q->fetch(); if(!$state || $state['user_status']!=='active' || $state['student_status']!=='active'){session_unset();session_destroy();header('Location: login.php?disabled=1');exit;} }
function require_admin(){ if(empty($_SESSION['user'])){header('Location: index.php');exit;} if(time()-(int)($_SESSION['last_activity']??time())>1800){session_unset();session_destroy();header('Location: index.php?timeout=1');exit;} $_SESSION['last_activity']=time(); if(($_SESSION['user']['role']??'')!=='admin'){http_response_code(403);exit('403 — administrator access only.');} }
function audit($pdo,$action,$details=''){ if(!empty($_SESSION['user']['id'])){ $s=$pdo->prepare('INSERT INTO audit_logs(user_id,action,details,ip_address) VALUES(?,?,?,?)');$s->execute([$_SESSION['user']['id'],$action,$details,substr($_SERVER['REMOTE_ADDR']??'',0,45)]); } }
function page_header($title='Student Portal') {
  $user=$_SESSION['user']??null;
  echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($title).' · Student Portal</title><link rel="stylesheet" href="portal.css"></head><body><header class="site-header"><a class="brand" href="index.php"><span class="brand-mark">SP</span><span><b>Student Portal</b><small>Learn · Connect · Participate</small></span></a><div class="header-right">';
  if($user){echo '<span class="welcome">Hi, '.e($user['name']).'</span><a class="logout-link" href="logout.php">Log out</a>';}else{echo '<a class="header-login" href="index.php">Sign in</a>';}
  echo '</div></header><div class="nav-wrap"><nav class="main-nav">';
  if($user && ($user['role']??'')==='admin'){
    echo '<a href="admin.php">Dashboard</a><a href="students.php">Manage Students</a><a href="event_manage.php">Manage Events</a><a href="contact_messages.php">Contact Messages</a><a href="audit_logs.php">Audit Logs</a>';
  }elseif($user){
    echo '<a href="home.php">Home</a><a href="about.php">About Us</a><a href="contact.php">Contact</a><a href="events.php">Register Events</a><a href="updateprofile.php">Update Profile</a>';
  }else{
    echo '<a href="index.php">Welcome</a><a href="login.php">Student Login</a><a href="register.php">Create Account</a><a href="admin_login.php">Admin Login</a>';
  }
  echo '</nav></div><main class="page-container">'; $m=flash(); if($m) echo '<div class="notice">'.e($m).'</div>';
}
function page_footer(){echo '</main><footer class="site-footer">@student portal</footer></body></html>';}
?>