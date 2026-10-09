<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/ClassSection.php';
require_once __DIR__ . '/../classes/Course.php';
require_once __DIR__ . '/../includes/header.php';

$classModel = new ClassSection($db);
$courseModel = new Course($db);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id  = (int)($_POST['course_id'] ?? 0);
    $class_code = trim($_POST['class_code'] ?? '');
    $schedule   = trim($_POST['schedule'] ?? '');
    $instructor = trim($_POST['instructor'] ?? '');
    $slots      = (int)($_POST['slots'] ?? 0);

    if ($course_id > 0 && $class_code && $slots >= 0) {
        $classModel->create($course_id, $class_code, $schedule, $instructor, $slots);
        $message = "Class section added successfully.";
    }
}

$classesList = $classModel->allWithCourse();
$courseOptions = $courseModel->all();
?>
<h2>Class Management</h2>
<?php if ($message): ?><div class="alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label>Course</label>
        <select name="course_id" required>
            <option value="">-- Select Course --</option>
            <?php foreach ($courseOptions as $co): ?>
                <option value="<?php echo $co['course_id']; ?>"><?php echo htmlspecialchars($co['course_code'] . ' - ' . $co['course_name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Class Code</label>
        <input type="text" name="class_code" required>
    </div>
    <div class="form-group">
        <label>Schedule</label>
        <input type="text" name="schedule" placeholder="e.g. MWF 9:00 - 11:00 AM" required>
    </div>
    <div class="form-group">
        <label>Instructor</label>
        <input type="text" name="instructor" required>
    </div>
    <div class="form-group">
        <label>Total Available Slots</label>
        <input type="number" name="slots" min="0" required>
    </div>
    <button type="submit">Create Class Section</button>
</form>

<table>
    <thead>
        <tr><th>ID</th><th>Course</th><th>Class Code</th><th>Schedule</th><th>Instructor</th><th>Remaining Slots</th></tr>
    </thead>
    <tbody>
        <?php foreach ($classesList as $cl): ?>
            <tr>
                <td><?php echo $cl['class_id']; ?></td>
                <td><?php echo htmlspecialchars($cl['course_name']); ?></td>
                <td><?php echo htmlspecialchars($cl['class_code']); ?></td>
                <td><?php echo htmlspecialchars($cl['schedule']); ?></td>
                <td><?php echo htmlspecialchars($cl['instructor']); ?></td>
                <td><strong><?php echo $cl['slots']; ?></strong></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>