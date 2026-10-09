<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

$reportSql = "SELECT co.course_name, c.class_code, c.instructor, c.slots AS remaining_slots,
                     COUNT(CASE WHEN e.status = 'active' THEN 1 END) AS active_enrolled,
                     COUNT(CASE WHEN e.status = 'cancelled' THEN 1 END) AS cancelled_count
              FROM classes c
              JOIN courses co ON c.course_id = co.course_id
              LEFT JOIN enrollments e ON c.class_id = e.class_id
              GROUP BY c.class_id
              ORDER BY co.course_name ASC, c.class_code ASC";

$reports = $db->query($reportSql)->fetchAll();
?>
<h2>Enrollment & Capacity Reports</h2>
<table>
    <thead>
        <tr>
            <th>Course</th>
            <th>Class Code</th>
            <th>Instructor</th>
            <th>Remaining Slots</th>
            <th>Active Students</th>
            <th>Cancelled Enrollments</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($reports as $rep): ?>
            <tr>
                <td><?php echo htmlspecialchars($rep['course_name']); ?></td>
                <td><?php echo htmlspecialchars($rep['class_code']); ?></td>
                <td><?php echo htmlspecialchars($rep['instructor']); ?></td>
                <td><?php echo $rep['remaining_slots']; ?></td>
                <td><?php echo $rep['active_enrolled']; ?></td>
                <td><?php echo $rep['cancelled_count']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>