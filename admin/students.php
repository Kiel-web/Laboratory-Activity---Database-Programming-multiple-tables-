<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';
require_once __DIR__ . '/../classes/ClassSection.php';
require_once __DIR__ . '/../includes/header.php';

$repo = new EnrollmentRepository($db);
$classes = new ClassSection($db);
$classList = $classes->allWithCourse();

$msg = '';
$isError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $class_id  = (int)($_POST['class_id'] ?? 0);

    if (empty($full_name) || $class_id <= 0) {
        $msg = "Please provide the student's full name and select a valid class.";
        $isError = true;
    } else {
        $result = $repo->recordStudent($full_name, $email, $phone, $class_id);
        if ($result) {
            $msg = "Student recorded and enrolled successfully! 1 seat deducted.";
            $classList = $classes->allWithCourse(); // Refresh slot counters
        } else {
            $msg = "No slots available or transaction failed. Record rolled back.";
            $isError = true;
        }
    }
}
?>
<h2>Record Student & Enroll</h2>
<?php if ($msg): ?>
    <div class="<?php echo $isError ? 'alert-error' : 'alert-success'; ?>">
        <?php echo htmlspecialchars($msg); ?>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="full_name" required>
    </div>
    <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email">
    </div>
    <div class="form-group">
        <label>Phone Number</label>
        <input type="text" name="phone">
    </div>
    <div class="form-group">
        <label>Select Class Section</label>
        <select name="class_id" required>
            <option value="">-- Choose Class --</option>
            <?php foreach ($classList as $cls): ?>
                <option value="<?php echo $cls['class_id']; ?>">
                    <?php echo htmlspecialchars($cls['class_code'] . ' - ' . $cls['course_name'] . ' (' . $cls['slots'] . ' slots remaining)'); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit">Record and Enroll Student</button>
</form>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>