<?php
require 'db.php'; require 'helpers.php'; require_admin();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    csrf_check();
    $id=(int)($_POST['message_id']??0); $action=$_POST['action']??'';
    if ($id>0 && $action==='mark_read') {
        $q=$pdo->prepare("UPDATE contact_messages SET status='read' WHERE id=?"); $q->execute([$id]);
        audit($pdo,'contact_message_read','Message ID '.$id); flash('Message marked as read.');
    } elseif ($id>0 && $action==='mark_unread') {
        $q=$pdo->prepare("UPDATE contact_messages SET status='unread' WHERE id=?"); $q->execute([$id]);
        audit($pdo,'contact_message_unread','Message ID '.$id); flash('Message marked as unread.');
    }
    header('Location: contact_messages.php'); exit;
}
$rows=$pdo->query("SELECT cm.*,u.username FROM contact_messages cm LEFT JOIN users u ON u.id=cm.user_id ORDER BY (cm.status='unread') DESC, cm.created_at DESC")->fetchAll();
page_header('Contact Messages');
$unread=(int)$pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status='unread'")->fetchColumn();
?>
<section class="hero"><span class="eyebrow">Student support inbox</span><h1>Contact Messages</h1><p>Messages sent by students from the Contact page appear here for the admin.</p><p><strong><?= $unread ?></strong> unread message(s).</p></section>
<section class="panel"><h2>Student messages</h2>
<?php if (!$rows): ?><p>No messages yet. When a student sends a message from Contact, it will appear here.</p><?php else: ?>
<div style="overflow-x:auto"><table><tr><th>Status</th><th>Student</th><th>Email</th><th>Subject</th><th>Message</th><th>Received</th><th>Action</th></tr>
<?php foreach($rows as $r): ?><tr><td><?= $r['status']==='unread' ? '<strong>Unread</strong>' : 'Read' ?></td><td><?=e($r['name'])?></td><td><?=e($r['email'])?></td><td><?=e($r['subject'])?></td><td style="white-space:pre-wrap;min-width:220px"><?=e($r['message'])?></td><td><?=e($r['created_at'])?></td><td><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="message_id" value="<?=$r['id']?>"><?php if($r['status']==='unread'): ?><button name="action" value="mark_read" type="submit">Mark Read</button><?php else: ?><button name="action" value="mark_unread" type="submit">Mark Unread</button><?php endif; ?></form></td></tr><?php endforeach; ?>
</table></div><?php endif; ?></section>
<?php page_footer(); ?>
