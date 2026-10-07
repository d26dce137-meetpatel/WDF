<?php if ($errors): ?>
    <div class="msg error">
        <?php foreach ($errors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= e($action) ?>">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <label>Name</label>
    <input type="text" name="name" value="<?= e($data['name']) ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?= e($data['email']) ?>" required>

    <label>Phone (10 digits)</label>
    <input type="text" name="phone" value="<?= e($data['phone']) ?>" maxlength="10" required>

    <label>Course</label>
    <select name="course" required>
        <option value="">-- Select --</option>
        <?php foreach (COURSES as $c): ?>
            <option value="<?= e($c) ?>" <?= $data['course'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit"><?= e($button) ?></button>
    <a href="index.php" class="btn gray">Cancel</a>
</form>