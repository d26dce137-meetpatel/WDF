<?php
require 'db.php';
require 'helpers.php';
require_student();
$msg=''; $err='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    csrf_check();
    $name=trim($_POST['name']??'');
    $email=trim($_POST['email']??'');
    $subject=trim($_POST['subject']??'');
    $message=trim($_POST['message']??'');
    if ($name==='' || !filter_var($email,FILTER_VALIDATE_EMAIL) || mb_strlen($subject)<3 || mb_strlen($message)<5) {
        $err='Please enter your name, a valid email, a subject of at least 3 characters, and a message of at least 5 characters.';
    } else {
        try {
            $stmt=$pdo->prepare("INSERT INTO contact_messages (user_id,name,email,subject,message,status) VALUES (?,?,?,?,?,'unread')");
            $stmt->execute([(int)($_SESSION['user']['id']??0),$name,$email,$subject,$message]);
            audit($pdo,'contact_message_sent','Subject: '.$subject);
            $msg='Message sent successfully. The admin can now view it in Admin Dashboard → Contact Messages.';
        } catch (Throwable $e) {
            $err='Message could not be saved. Please check that the contact_messages table exists in the database.';
        }
    }
}
page_header('Contact Us');
if($msg) echo '<div class="notice">'.e($msg).'</div>';
if($err) echo '<div class="error">'.e($err).'</div>';
?>
<div class="auth-layout"><section class="auth-aside"><span class="eyebrow">We're here to help</span><h1>Have a question?</h1><p>Send a message to the Student Portal admin. Your message is saved in the portal inbox for the admin to review.</p><p>Typical topics: event registration, profile details, and portal access.</p></section><section class="auth-card"><h2>Contact the admin</h2><p class="muted">Your message will be delivered to the Admin Contact Messages inbox.</p><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><label for="name">Full name</label><input id="name" name="name" required value="<?=e($_SESSION['user']['name']??'')?>"><label for="email">Email address</label><input id="email" type="email" name="email" required value="<?=e($_SESSION['user']['email']??'')?>"><label for="subject">Subject</label><input id="subject" name="subject" required minlength="3" placeholder="What do you need help with?"><label for="message">Message</label><textarea id="message" name="message" required minlength="5" placeholder="Write your message here..."></textarea><button type="submit">Send message →</button></form></section></div>
<?php page_footer(); ?>
