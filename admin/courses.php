<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/Course.php';
require_once __DIR__ . '/../includes/header.php';

$courseModel = new Course($db);
$message = '';
$editCourse = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $code = trim($_POST['course_code'] ?? '');
    $name = trim($_POST['course_name'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    if ($action === 'create' && $code && $name) {
        $courseModel->create($code, $name, $desc);
        $message = "Course added successfully.";
    } elseif ($action === 'update' && !empty($_POST['course_id']) && $code && $name) {
        $courseModel->update((int)$_POST['course_id'], $code, $name, $desc);
        $message = "Course updated successfully.";
    }
}

if (isset($_GET['delete'])) {
    $courseModel->delete((int)$_GET['delete']);
    $message = "Course deleted.";
}

if (isset($_GET['edit'])) {
    $editCourse = $courseModel->find((int)$_GET['edit']);
}

$courses = $courseModel->all();
?>
<h2>Course Management</h2>
<?php if ($message): ?><div class="alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

<form method="POST">
    <input type="hidden" name="action" value="<?php echo $editCourse ? 'update' : 'create'; ?>">
    <?php if ($editCourse): ?>
        <input type="hidden" name="course_id" value="<?php echo $editCourse['course_id']; ?>">
    <?php endif; ?>
    <div class="form-group">
        <label>Course Code</label>
        <input type="text" name="course_code" required value="<?php echo htmlspecialchars($editCourse['course_code'] ?? ''); ?>">
    </div>
    <div class="form-group">
        <label>Course Name</label>
        <input type="text" name="course_name" required value="<?php echo htmlspecialchars($editCourse['course_name'] ?? ''); ?>">
    </div>
    <div class="form-group">
        <label>Description</label>
        <textarea name="description"><?php echo htmlspecialchars($editCourse['description'] ?? ''); ?></textarea>
    </div>
    <button type="submit"><?php echo $editCourse ? 'Update Course' : 'Add Course'; ?></button>
    <?php if ($editCourse): ?><a href="courses.php">Cancel</a><?php endif; ?>
</form>

<table>
    <thead>
        <tr><th>ID</th><th>Code</th><th>Name</th><th>Description</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach ($courses as $c): ?>
            <tr>
                <td><?php echo $c['course_id']; ?></td>
                <td><?php echo htmlspecialchars($c['course_code']); ?></td>
                <td><?php echo htmlspecialchars($c['course_name']); ?></td>
                <td><?php echo htmlspecialchars($c['description']); ?></td>
                <td>
                    <a href="courses.php?edit=<?php echo $c['course_id']; ?>">Edit</a> |
                    <a href="courses.php?delete=<?php echo $c['course_id']; ?>" onclick="return confirm('Delete course?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>