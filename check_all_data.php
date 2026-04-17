<?php
$conn = new mysqli('localhost', 'root', 'Tinkerbell8877', 'college_academic_portal');

echo "=== STUDENT ENROLLMENTS ===\n";
$result = $conn->query("SELECT * FROM student_enrollments");
while ($row = $result->fetch_assoc()) {
    echo "Student ID: {$row['student_id']}, Subject ID: {$row['subject_id']}\n";
}

echo "\n=== MAPPING USERS TO STUDENTS ===\n";
$result = $conn->query("SELECT u.id as user_id, u.username, s.id as student_id, s.current_semester 
                       FROM users u 
                       LEFT JOIN students s ON u.id = s.user_id 
                       WHERE u.role = 'student'");
while ($row = $result->fetch_assoc()) {
    echo "User {$row['user_id']} ({$row['username']}) -> Student {$row['student_id']} (Sem {$row['current_semester']})\n";
}

$conn->close();
?>