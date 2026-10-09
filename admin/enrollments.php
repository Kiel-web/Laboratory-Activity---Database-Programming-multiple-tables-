<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';
require_once __DIR__ . '/../includes/header.php';

$repo = new EnrollmentRepository($db);
$msg = '';

if (isset($_GET['cancel_id'])) {
    $cancelId = (int)$_GET['cancel_id'];
    if ($repo->cancel($cancelId)) {
        $msg = "Enrollment #$cancelId successfully cancelled and class slot restored.";
    } else {
        $msg = "Unable to cancel enrollment #$cancelId.";
    }
}

$rows = $repo->allWithDetails();
?>
<h2>Enrollment Records</h2>
<?php if ($msg): ?><div class="alert-success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Student</th>
            <th>Course</th>
            <th>Class</th>
            <th>Schedule</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($rows)): ?>
            <tr><td colspan="8">No enrollments recorded yet.</td></tr>
        <?php else: ?>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?php echo $r['enrollment_id']; ?></td>
                    <td><?php echo date('Y-m-d H:i', strtotime($r['enrollment_date'])); ?></td>
                    <td><?php echo htmlspecialchars($r['full_name']); ?><br><small><?php echo htmlspecialchars($r['email']); ?></small></td>
                    <td><?php echo htmlspecialchars($r['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($r['class_code']); ?></td>
                    <td><?php echo htmlspecialchars($r['schedule']); ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars(strtoupper($r['status'])); ?></strong>
                    </td>
                    <td>
                        <?php if ($r['status'] === 'active'): ?>
                            <a href="enrollments.php?cancel_id=<?php echo $r['enrollment_id']; ?>" 
                               onclick="return confirm('Cancel this enrollment? The class slot will be returned.');">Cancel</a>
                        <?php else: ?>
                            <em>None</em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>