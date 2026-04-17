<?php
$conn = new mysqli('localhost', 'root', 'Tinkerbell8877', 'college_academic_portal');

echo "=== ALL STUDENTS ===\n";
$result = $conn->query("SELECT * FROM students");
while ($row = $result->fetch_assoc()) {
    echo "ID: {$row['id']}, Semester: {$row['current_semester']}, Dept: {$row['department_id']}\n";
}

echo "\n=== ALL USERS ===\n";
$result = $conn->query("SELECT * FROM users WHERE role = 'student'");
while ($row = $result->fetch_assoc()) {
    echo "User ID: {$row['id']}, Username: {$row['username']}, Role: {$row['role']}\n";
}

$conn->close();
?>