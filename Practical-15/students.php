<?php
require 'db.php';
require 'helpers.php';
require_admin();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($act === 'delete' && $id > 0) {
        $s = $pdo->prepare("SELECT user_id,email,name FROM students WHERE id=? AND status='active'");
        $s->execute([$id]); $student = $s->fetch();
        if ($student) {
            try {
                $pdo->beginTransaction();
                $pdo->prepare("UPDATE students SET status='deleted' WHERE id=?")->execute([$id]);
                if (!empty($student['user_id'])) $pdo->prepare("UPDATE users SET status='inactive' WHERE id=? AND role='student'")->execute([(int)$student['user_id']]);
                else $pdo->prepare("UPDATE users SET status='inactive' WHERE email=? AND role='student'")->execute([$student['email']]);
                $pdo->commit();
                audit($pdo, 'student_delete_to_trash', 'Student ID '.$id.' ('.$student['name'].') moved to trash; registrations retained');
                $msg = 'Student moved to Deleted Students. Restore is available until you permanently delete the record.';
            } catch (Throwable $ex) { if ($pdo->inTransaction()) $pdo->rollBack(); $msg = 'Delete failed: '.$ex->getMessage(); }
        } else $msg = 'Active student record was not found.';
    } elseif ($act === 'restore' && $id > 0) {
        $s = $pdo->prepare("SELECT user_id,email,name FROM students WHERE id=? AND status='deleted'"); $s->execute([$id]); $student = $s->fetch();
        if ($student) {
            try {
                $pdo->beginTransaction();
                $pdo->prepare("UPDATE students SET status='active' WHERE id=? AND status='deleted'")->execute([$id]);
                if (!empty($student['user_id'])) $pdo->prepare("UPDATE users SET status='active' WHERE id=? AND role='student'")->execute([(int)$student['user_id']]);
                else $pdo->prepare("UPDATE users SET status='active' WHERE email=? AND role='student'")->execute([$student['email']]);
                $pdo->commit(); audit($pdo, 'student_restore', 'Restored student ID '.$id.' ('.$student['name'].') and linked login');
                $msg = 'Student restored successfully. Their login and event registrations are retained.';
            } catch (Throwable $ex) { if ($pdo->inTransaction()) $pdo->rollBack(); $msg = 'Restore failed: '.$ex->getMessage(); }
        } else $msg = 'Deleted student record was not found.';
    } elseif ($act === 'permanent_delete' && $id > 0) {
        $s = $pdo->prepare("SELECT user_id,email,name FROM students WHERE id=? AND status='deleted'"); $s->execute([$id]); $student = $s->fetch();
        if ($student) {
            try {
                $pdo->beginTransaction();
                // Keep audit history, but permanently remove student + registrations + linked student login.
                $pdo->prepare('DELETE FROM students WHERE id=?')->execute([$id]); // registrations cascade by FK
                if (!empty($student['user_id'])) $pdo->prepare("DELETE FROM users WHERE id=? AND role='student'")->execute([(int)$student['user_id']]);
                else $pdo->prepare("DELETE FROM users WHERE email=? AND role='student'")->execute([$student['email']]);
                $pdo->commit(); audit($pdo, 'student_permanent_delete', 'Permanently deleted student ID '.$id.' ('.$student['name'].') and linked login');
                $msg = 'Student permanently deleted. This action cannot be undone.';
            } catch (Throwable $ex) { if ($pdo->inTransaction()) $pdo->rollBack(); $msg = 'Permanent delete failed: '.$ex->getMessage(); }
        } else $msg = 'Student not found in Deleted Students.';
    }
}
$q=trim($_GET['q']??''); $course=trim($_GET['course']??'');
$sql="SELECT * FROM students WHERE status='active'"; $args=[];
if($q!==''){$sql.=' AND (name LIKE ? OR email LIKE ? OR enrollment_number LIKE ?)';array_push($args,"%$q%","%$q%","%$q%");}
if($course!==''){$sql.=' AND course=?';$args[]=$course;}
$sql.=' ORDER BY id DESC LIMIT 200';$s=$pdo->prepare($sql);$s->execute($args);$rows=$s->fetchAll();
$deletedRows=$pdo->query("SELECT * FROM students WHERE status='deleted' ORDER BY id DESC LIMIT 200")->fetchAll();
page_header('Student Management'); if($msg) echo '<div class="notice">'.e($msg).'</div>';
?>
<section class="panel"><h2>Active Student Records</h2><form method="get"><input name="q" placeholder="Search name/email/enrollment" value="<?=e($q)?>"><input name="course" placeholder="Filter course" value="<?=e($course)?>"><button>Search / Filter</button> <a class="btn" href="export_students.php">Export CSV</a></form><table><tr><th>ID</th><th>Enrollment</th><th>Name</th><th>Email</th><th>Course</th><th>Action</th></tr><?php if(!$rows):?><tr><td colspan="6">No active students found.</td></tr><?php endif;?><?php foreach($rows as $r):?><tr><td><?=e($r['id'])?></td><td><?=e($r['enrollment_number'])?></td><td><?=e($r['name'])?></td><td><?=e($r['email'])?></td><td><?=e($r['course'])?></td><td><form method="post" onsubmit="return confirm('Move this student to Deleted Students? You can restore before permanent deletion.')"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=e($r['id'])?>"><button>Delete</button></form></td></tr><?php endforeach;?></table></section>
<section class="panel"><h2>Deleted Students / Restore</h2><p>Delete removes a student from active lists and disables login. Restore returns them and preserves registrations. Use Permanent Delete only when you are sure; it removes the student, linked login and registrations.</p><table><tr><th>ID</th><th>Enrollment</th><th>Name</th><th>Email</th><th>Course</th><th>Actions</th></tr><?php if(!$deletedRows):?><tr><td colspan="6">Trash is empty.</td></tr><?php endif;?><?php foreach($deletedRows as $r):?><tr><td><?=e($r['id'])?></td><td><?=e($r['enrollment_number'])?></td><td><?=e($r['name'])?></td><td><?=e($r['email'])?></td><td><?=e($r['course'])?></td><td><form method="post" style="display:inline-block"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="restore"><input type="hidden" name="id" value="<?=e($r['id'])?>"><button>Restore</button></form> <form method="post" style="display:inline-block" onsubmit="return confirm('Permanently delete this student, their login, and event registrations? This cannot be undone.')"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="permanent_delete"><input type="hidden" name="id" value="<?=e($r['id'])?>"><button class="btn-danger">Permanently Delete</button></form></td></tr><?php endforeach;?></table></section>
<?php page_footer(); ?>
