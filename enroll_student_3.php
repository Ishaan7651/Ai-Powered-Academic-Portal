<?php
$conn = new mysqli('localhost', 'root', 'Tinkerbell8877', 'college_academic_portal');

echo "Enrolling student 3 in Clinical Psychology...\n";

// Check current enrollments
$result = $conn->query("SELECT * FROM student_enrollments WHERE student_id = 3");
echo "Current enrollments for student 3: " . $result->num_rows . "\n";

// Enroll in Clinical Psychology
$insert = $conn->query("INSERT INTO student_enrollments (student_id, subject_id) VALUES (3, 7)");
if ($insert) {
    echo "✅ Successfully enrolled student 3 in Clinical Psychology\n";
} else {
    echo "❌ Error: " . $conn->error . "\n";
}

// Verify
$result = $conn->query("SELECT se.*, s.subject_name FROM student_enrollments se JOIN subjects s ON se.subject_id = s.id WHERE se.student_id = 3");
echo "\nUpdated enrollments for student 3:\n";
while ($row = $result->fetch_assoc()) {
    echo "- {$row['subject_name']} (ID: {$row['subject_id']})\n";
}

$conn->close();
?>