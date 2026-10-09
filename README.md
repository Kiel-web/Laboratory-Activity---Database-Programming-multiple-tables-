 Training Enrollment System (Multi-Table PHP PDO Application)

1. Project Description
The Training Enrollment System is a database-driven web application developed with PHP and PDO It models a training center catalog where courses have scheduled classes with finite capacities.
A single administrator manages course offerings, creates class sections, registers students, processes enrollments, and tracks summative statistics.

The application enforces relational data integrity across multiple tables (courses, classes, students, enrollments) by preventing duplicate entries, managing seat limits, handling enrollment cancellations, and utilizing database transactions for atomic operations.

2. System Features
* Course Catalog Management (CRUD): Add, view, edit, and delete training course offerings
* Class Section Scheduling: Manage class sections with associated course details, instructors, schedules, and remaining seat counts
* Atomic Multi-Table Student Registration: Record a new student, create their enrollment, and decrement available class slots in a single atomic transaction
* Returning Student Enrollment: Book seats for existing students with slot availability validation
* Enrollment Roster & Cancellation: View enrollments with SQL multi-table joins and cancel active enrollments while automatically restoring class slots
* Summative Capacity Reports: Aggregate active enrollments, cancellations, and remaining seat allocations

 3. Design Patterns Used

1. Singleton Pattern (classes/Database.php)
* What it is: Restricts the instantiation of the custom `Database` class (which extends `PDO`) to a single shared instance throughout the request lifecycle
* Why it was used:
  * Prevents overhead caused by opening multiple database connections per request
  * Ensures that transactional operations (beginTransaction, commit, rollBack) operate predictably on the exact same database connection handle
2. Repository Pattern (classes/EnrollmentRepository.php)
* What it is: Acts as an abstraction layer between domain business logic and the database data-access layer
* Why it was used:
  * Centralizes multi-table database operations and isolates SQL statements from UI presentation scripts (`admin/` files)
  * Manages transaction boundaries cleanly. Methods such as `recordStudent()` encapsulate the three-step operation (insert student, create enrollment, decrement seat count) within a unified try-catch block, rolling back all queries if any step fails or if slots are exhausted

4. Database Setup & Installation

Prerequisites
* Web Server & Database: XAMPP (Apache, MySQL)
* PHP Version: 8.0+
* Web Browser
