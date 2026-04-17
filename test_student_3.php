<?php
/**
 * Test what Student ID 3 can see
 */

$host = 'localhost';
$username = 'root';
$password = 'Tinkerbell8877';
$database = 'college_academic_portal';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

$student_id = 3; // This student has enrollments
echo "=== TESTING STUDENT ID $student_id ===\n\n";

// Check enrollments
echo "Student $student_id enrollments:\n";
$result = $conn->query("SELECT se.*, s.subject_name, s.id as subject_id 
                       FROM student_enrollments se 
                       JOIN subjects s ON se.subject_id = s.id 
                       WHERE se.student_id = $student_id");
while ($row = $result->fetch_assoc()) {
    echo "- Subject: {$row['subject_name']} (ID: {$row['subject_id']})\n";
}

echo "\n";

// Check what published content they should see
echo "Published assignments for student $student_id:\n";
$query = "SELECT a.id, a.title, s.subject_name 
          FROM ai_assignments a 
          JOIN subjects s ON a.subject_id = s.id 
          JOIN student_enrollments se ON a.subject_id = se.subject_id 
          WHERE se.student_id = ? AND a.is_published = 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "- Assignment: {$row['title']} (Subject: {$row['subject_name']})\n";
    }
} else {
    echo "- No assignments found\n";
}

echo "\nPublished question papers for student $student_id:\n";
$query = "SELECT qp.id, qp.title, s.subject_name 
          FROM ai_question_papers qp 
          JOIN subjects s ON qp.subject_id = s.id 
          JOIN student_enrollments se ON qp.subject_id = se.subject_id 
          WHERE se.student_id = ? AND qp.is_published = 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "- Question Paper: {$row['title']} (Subject: {$row['subject_name']})\n";
    }
} else {
    echo "- No question papers found\n";
}

echo "\nPublished quizzes for student $student_id:\n";
$query = "SELECT q.id, q.title, s.subject_name 
          FROM ai_quizzes q 
          JOIN subjects s ON q.subject_id = s.id 
          JOIN student_enrollments se ON q.subject_id = se.subject_id 
          WHERE se.student_id = ? AND q.is_published = 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "- Quiz: {$row['title']} (Subject: {$row['subject_name']})\n";
    }
} else {
    echo "- No quizzes found\n";
}

// Check what subjects have published content
echo "\n=== ALL PUBLISHED CONTENT BY SUBJECT ===\n";
$result = $conn->query("SELECT DISTINCT s.id, s.subject_name 
                       FROM subjects s 
                       WHERE s.id IN (
                           SELECT subject_id FROM ai_assignments WHERE is_published = 1
                           UNION 
                           SELECT subject_id FROM ai_question_papers WHERE is_published = 1
                           UNION 
                           SELECT subject_id FROM ai_quizzes WHERE is_published = 1
                       )");

while ($row = $result->fetch_assoc()) {
    echo "Subject: {$row['subject_name']} (ID: {$row['id']})\n";
    
    // Check if student 3 is enrolled
    $enrolled = $conn->query("SELECT 1 FROM student_enrollments WHERE student_id = $student_id AND subject_id = {$row['id']}")->num_rows > 0;
    echo "  - Student $student_id enrolled: " . ($enrolled ? "YES" : "NO") . "\n";
}

$conn->close();
?>