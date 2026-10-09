<?php
require_once __DIR__ . '/includes/header.php';
?>
<h1>Training Enrollment System</h1>
<p>Welcome to the Administrative Dashboard. Select a module to begin:</p>
<ul>
    <li><a href="admin/courses.php">Manage Courses</a> (Course catalog and offerings)</li>
    <li><a href="admin/classes.php">Manage Class Sections</a> (Schedules and seat caps)</li>
    <li><a href="admin/students.php">Record Student & Enroll</a> (Atomic registration & seat booking)</li>
    <li><a href="admin/enroll.php">Enroll Existing Student</a> (Seat booking for returning trainees)</li>
    <li><a href="admin/enrollments.php">View & Cancel Enrollments</a> (Active roster and cancellations)</li>
    <li><a href="admin/reports.php">Summary Reports</a> (Capacity and enrollment analytics)</li>
</ul>
<?php require_once __DIR__ . '/includes/footer.php'; ?>