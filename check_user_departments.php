<?php
// Direct database check for user departments
$conn = new mysqli('localhost', 'root', 'Tinkerbell8877', 'college_academic_portal');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Users with Department Information</h2>";

// Get all users
$users_query = "SELECT id, username, email, role FROM users ORDER BY role, username";
$users_result = $conn->query($users_query);

echo "<pre>";
while ($user = $users_result->fetch_assoc()) {
    echo "\n=== {$user['username']} (ID: {$user['id']}, Role: {$user['role']}) ===\n";
    
    if ($user['role'] === 'faculty') {
        // Get faculty departments
        $faculty_query = "
            SELECT f.id as faculty_id, f.employee_id, GROUP_CONCAT(d.name SEPARATOR ', ') as departments
            FROM faculty f
            LEFT JOIN faculty_departments fd ON f.id = fd.faculty_id
            LEFT JOIN departments d ON fd.department_id = d.id
            WHERE f.user_id = {$user['id']}
            GROUP BY f.id
        ";
        $faculty_result = $conn->query($faculty_query);
        if ($faculty_row = $faculty_result->fetch_assoc()) {
            echo "Employee ID: " . ($faculty_row['employee_id'] ?: 'N/A') . "\n";
            echo "Departments: " . ($faculty_row['departments'] ?: 'No department assigned') . "\n";
        } else {
            echo "No faculty record found\n";
        }
    } elseif ($user['role'] === 'student') {
        // Get student department
        $student_query = "
            SELECT s.student_id, s.current_semester, s.enrollment_year, s.department_id, d.name as department
            FROM students s
            LEFT JOIN departments d ON s.department_id = d.id
            WHERE s.user_id = {$user['id']}
        ";
        $student_result = $conn->query($student_query);
        if ($student_row = $student_result->fetch_assoc()) {
            echo "Student ID: " . ($student_row['student_id'] ?: 'N/A') . "\n";
            echo "Department ID: " . ($student_row['department_id'] ?: 'NULL') . "\n";
            echo "Department: " . ($student_row['department'] ?: 'No department assigned') . "\n";
            echo "Semester: " . ($student_row['current_semester'] ?: 'N/A') . "\n";
            echo "Year: " . ($student_row['enrollment_year'] ?: 'N/A') . "\n";
        } else {
            echo "No student record found\n";
        }
    }
}
echo "</pre>";

$conn->close();
?>
