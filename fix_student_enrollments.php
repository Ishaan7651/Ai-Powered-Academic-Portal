<?php
/**
 * Fix Student Enrollments - Enroll students in Clinical Psychology
 */

$host = 'localhost';
$username = 'root';
$password = 'Tinkerbell8877';
$database = 'college_academic_portal';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

echo "=== FIXING STUDENT ENROLLMENTS ===\n\n";

// Get Clinical Psychology subject ID
$result = $conn->query("SELECT id FROM subjects WHERE subject_name = 'Clinical Psychology'");
if ($result->num_rows == 0) {
    die("Clinical Psychology subject not found!\n");
}
$clinical_psych_id = $result->fetch_assoc()['id'];
echo "Clinical Psychology subject ID: $clinical_psych_id\n";

// Get all students
$result = $conn->query("SELECT id FROM students");
$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row['id'];
}

echo "Found " . count($students) . " students\n";

// Enroll each student in Clinical Psychology (if not already enrolled)
foreach ($students as $student_id) {
    // Check if already enrolled
    $check = $conn->query("SELECT 1 FROM student_enrollments WHERE student_id = $student_id AND subject_id = $clinical_psych_id");
    
    if ($check->num_rows == 0) {
        // Not enrolled, add enrollment
        $insert = $conn->query("INSERT INTO student_enrollments (student_id, subject_id) VALUES ($student_id, $clinical_psych_id)");
        if ($insert) {
            echo "✅ Enrolled student $student_id in Clinical Psychology\n";
        } else {
            echo "❌ Failed to enroll student $student_id: " . $conn->error . "\n";
        }
    } else {
        echo "ℹ️  Student $student_id already enrolled in Clinical Psychology\n";
    }
}

echo "\n=== VERIFICATION ===\n";

// Test with student 3 again
$student_id = 3;
echo "Testing student $student_id after enrollment:\n";

$query = "SELECT a.id, a.title, s.subject_name 
          FROM ai_assignments a 
          JOIN subjects s ON a.subject_id = s.id 
          JOIN student_enrollments se ON a.subject_id = se.subject_id 
          WHERE se.student_id = ? AND a.is_published = 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

echo "Published assignments visible: " . $result->num_rows . "\n";
while ($row = $result->fetch_assoc()) {
    echo "- {$row['title']} (Subject: {$row['subject_name']})\n";
}

$query = "SELECT qp.id, qp.title, s.subject_name 
          FROM ai_question_papers qp 
          JOIN subjects s ON qp.subject_id = s.id 
          JOIN student_enrollments se ON qp.subject_id = se.subject_id 
          WHERE se.student_id = ? AND qp.is_published = 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

echo "Published question papers visible: " . $result->num_rows . "\n";
while ($row = $result->fetch_assoc()) {
    echo "- {$row['title']} (Subject: {$row['subject_name']})\n";
}

$conn->close();
echo "\nDone! Students should now see published content.\n";
?>