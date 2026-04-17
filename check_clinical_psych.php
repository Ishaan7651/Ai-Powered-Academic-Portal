<?php
$conn = new mysqli('localhost', 'root', 'Tinkerbell8877', 'college_academic_portal');

echo "=== CLINICAL PSYCHOLOGY DETAILS ===\n";
$result = $conn->query("SELECT * FROM subjects WHERE subject_name = 'Clinical Psychology'");
if ($row = $result->fetch_assoc()) {
    echo "Subject: {$row['subject_name']}\n";
    echo "Subject ID: {$row['id']}\n";
    echo "Semester: {$row['semester']}\n";
} else {
    echo "Clinical Psychology not found!\n";
}

echo "\n=== STUDENT CURRENT SEMESTERS ===\n";
$result = $conn->query("SELECT id, current_semester FROM students LIMIT 5");
while ($row = $result->fetch_assoc()) {
    echo "Student {$row['id']}: Semester {$row['current_semester']}\n";
}

$conn->close();
?>