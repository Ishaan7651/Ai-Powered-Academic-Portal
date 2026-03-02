<?php
$hostname = 'localhost';
$username = 'root';
$password = 'Tinkerbell8877';
$database = 'college_academic_portal';
$conn = new mysqli($hostname, $username, $password, $database);

echo "<h3>Faculty with departments:</h3><pre>";
$result = $conn->query("SELECT f.id, f.user_id, f.employee_id, GROUP_CONCAT(d.name SEPARATOR ', ') as departments 
FROM faculty f 
LEFT JOIN faculty_departments fd ON f.id = fd.faculty_id 
LEFT JOIN departments d ON fd.department_id = d.id 
GROUP BY f.id");
while ($row = $result->fetch_assoc()) {
    echo "Faculty ID: {$row['id']}, User ID: {$row['user_id']}, Departments: " . ($row['departments'] ?: 'NULL') . "\n";
}

echo "\n<h3>Students with departments:</h3>";
$result = $conn->query("SELECT s.id, s.user_id, s.current_semester, d.name as department 
FROM students s 
LEFT JOIN departments d ON s.department_id = d.id");
while ($row = $result->fetch_assoc()) {
    echo "Student ID: {$row['id']}, User ID: {$row['user_id']}, Semester: {$row['current_semester']}, Dept: " . ($row['department'] ?: 'NULL') . "\n";
}

echo "</pre>";
$conn->close();
?>
