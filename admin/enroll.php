<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';
require_once __DIR__ . '/../classes/Student.php';
require_once __DIR__ . '/../classes/ClassSection.php';
require_once __DIR__ . '/../includes/header.php';

$repo = new EnrollmentRepository($db);
$studentModel = new Student($db);
$classModel = new ClassSection($db);

$msg = '';
$isError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)($_POST['student_id'] ?? 0);
    $class_id   = (int)($_POST['class_id'] ?? 0);

    if ($student_id > 0 && $class_id > 0) {
        $ok = $repo->enroll($student_id, $class_id);
        if ($ok) {
            $msg = "Existing student enrolled successfully!";
        } else {
            $msg = "No slots available or enrollment could not be completed.";
            $isError = true;
        }
    } else {
        $msg = "Please select both a student and a class.";
        $isError = true;
    }
}

$studentsList = $studentModel->all();
$classList = $classModel->allWithCourse();
?>
<h2>Enroll Existing Student</h2>
<?php if ($msg): ?>
    <div class="<?php echo $isError ? 'alert-error' : 'alert-success'; ?>">
        <?php echo htmlspecialchars($msg); ?>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label>Student</label>
        <select name="student_id" required>
            <option value="">-- Choose Student --</option>
            <?php foreach ($studentsList as $st): ?>
                <option value="<?php echo $st['student_id']; ?>"><?php echo htmlspecialchars($st['full_name'] . ' (' . $st['email'] . ')'); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Class Section</label>
        <select name="class_id" required>
            <option value="">-- Choose Class --</option>
            <?php foreach ($classList as $cl): ?>
                <option value="<?php echo $cl['class_id']; ?>">
                    <?php echo htmlspecialchars($cl['class_code'] . ' - ' . $cl['course_name'] . ' (' . $cl['slots'] . ' slots left)'); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit">Process Enrollment</button>
</form>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>