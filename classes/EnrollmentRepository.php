<?php
class EnrollmentRepository {
    private $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function recordStudent($full_name, $email, $phone, $class_id) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT slots FROM classes WHERE class_id = :id FOR UPDATE");
            $stmt->execute(['id' => $class_id]);
            $cls = $stmt->fetch();

            if (!$cls || (int)$cls['slots'] <= 0) {
                $this->db->rollBack();
                return false;
            }

            $stmt = $this->db->prepare("INSERT INTO students (full_name, email, phone) VALUES (:name, :email, :phone)");
            $stmt->execute(['name' => $full_name, 'email' => $email, 'phone' => $phone]);
            $student_id = $this->db->lastInsertId();

            $stmt = $this->db->prepare("INSERT INTO enrollments (student_id, class_id, status) VALUES (:student_id, :class_id, 'active')");
            $stmt->execute(['student_id' => $student_id, 'class_id' => $class_id]);

            $stmt = $this->db->prepare("UPDATE classes SET slots = slots - 1 WHERE class_id = :id");
            $stmt->execute(['id' => $class_id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function enroll($student_id, $class_id) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT slots FROM classes WHERE class_id = :id FOR UPDATE");
            $stmt->execute(['id' => $class_id]);
            $cls = $stmt->fetch();

            if (!$cls || (int)$cls['slots'] <= 0) {
                $this->db->rollBack();
                return false;
            }

            $stmt = $this->db->prepare("INSERT INTO enrollments (student_id, class_id, status) VALUES (:student_id, :class_id, 'active')");
            $stmt->execute(['student_id' => $student_id, 'class_id' => $class_id]);

            $stmt = $this->db->prepare("UPDATE classes SET slots = slots - 1 WHERE class_id = :id");
            $stmt->execute(['id' => $class_id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function cancel($enrollment_id) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT class_id, status FROM enrollments WHERE enrollment_id = :id FOR UPDATE");
            $stmt->execute(['id' => $enrollment_id]);
            $enr = $stmt->fetch();

            if (!$enr || $enr['status'] === 'cancelled') {
                $this->db->rollBack();
                return false;
            }

            $stmt = $this->db->prepare("UPDATE enrollments SET status = 'cancelled' WHERE enrollment_id = :id");
            $stmt->execute(['id' => $enrollment_id]);

            $stmt = $this->db->prepare("UPDATE classes SET slots = slots + 1 WHERE class_id = :id");
            $stmt->execute(['id' => $enr['class_id']]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function allWithDetails() {
        $sql = "SELECT e.enrollment_id, e.enrollment_date, e.status,
                       s.full_name, s.email, s.phone,
                       c.class_code, c.schedule, c.instructor,
                       co.course_name, co.course_code
                FROM enrollments e
                JOIN students s ON e.student_id = s.student_id
                JOIN classes c ON e.class_id = c.class_id
                JOIN courses co ON c.course_id = co.course_id
                ORDER BY e.enrollment_id DESC";
        return $this->db->query($sql)->fetchAll();
    }
}