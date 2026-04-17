<?php
/**
 * Check Student Enrollments and Published Content
 */

$host = 'localhost';
$username = 'root';
$password = 'Tinkerbell8877';
$database = 'college_academic_portal';

echo "Connecting to database...\n";
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

echo "Connected successfully!\n\n";

// Check if student_enrollments table exists
echo "=== CHECKING TABLES ===\n";
$tables_to_check = ['student_enrollments', 'ai_quizzes', 'ai_question_papers', 'ai_assignments', 'subjects', 'students'];

foreach ($tables_to_check as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "✅ Table '$table' exists\n";
    } else {
        echo "❌ Table '$table' MISSING\n";
    }
}

echo "\n=== CHECKING STUDENT ENROLLMENTS ===\n";
if ($conn->query("SHOW TABLES LIKE 'student_enrollments'")->num_rows > 0) {
    $result = $conn->query("SELECT COUNT(*) as count FROM student_enrollments");
    $count = $result->fetch_assoc()['count'];
    echo "Total enrollments: $count\n";
    
    if ($count > 0) {
        echo "\nSample enrollments:\n";
        $result = $conn->query("SELECT se.*, s.subject_name 
                               FROM student_enrollments se 
                               LEFT JOIN subjects s ON se.subject_id = s.id 
                               LIMIT 5");
        while ($row = $result->fetch_assoc()) {
            echo "- Student ID: {$row['student_id']} -> Subject: {$row['subject_name']} (ID: {$row['subject_id']})\n";
        }
    }
} else {
    echo "❌ student_enrollments table does not exist!\n";
}

echo "\n=== CHECKING PUBLISHED CONTENT ===\n";

// Check published quizzes
if ($conn->query("SHOW TABLES LIKE 'ai_quizzes'")->num_rows > 0) {
    $result = $conn->query("SELECT COUNT(*) as count FROM ai_quizzes WHERE is_published = 1");
    $count = $result->fetch_assoc()['count'];
    echo "Published quizzes: $count\n";
    
    if ($count > 0) {
        $result = $conn->query("SELECT q.id, q.title, s.subject_name, q.published_at 
                               FROM ai_quizzes q 
                               LEFT JOIN subjects s ON q.subject_id = s.id 
                               WHERE q.is_published = 1 
                               LIMIT 3");
        while ($row = $result->fetch_assoc()) {
            echo "- Quiz: {$row['title']} (Subject: {$row['subject_name']}) - Published: {$row['published_at']}\n";
        }
    }
}

// Check published question papers
if ($conn->query("SHOW TABLES LIKE 'ai_question_papers'")->num_rows > 0) {
    $result = $conn->query("SELECT COUNT(*) as count FROM ai_question_papers WHERE is_published = 1");
    $count = $result->fetch_assoc()['count'];
    echo "Published question papers: $count\n";
    
    if ($count > 0) {
        $result = $conn->query("SELECT qp.id, qp.title, s.subject_name, qp.published_at 
                               FROM ai_question_papers qp 
                               LEFT JOIN subjects s ON qp.subject_id = s.id 
                               WHERE qp.is_published = 1 
                               LIMIT 3");
        while ($row = $result->fetch_assoc()) {
            echo "- Paper: {$row['title']} (Subject: {$row['subject_name']}) - Published: {$row['published_at']}\n";
        }
    }
}

// Check published assignments
if ($conn->query("SHOW TABLES LIKE 'ai_assignments'")->num_rows > 0) {
    $result = $conn->query("SELECT COUNT(*) as count FROM ai_assignments WHERE is_published = 1");
    $count = $result->fetch_assoc()['count'];
    echo "Published assignments: $count\n";
    
    if ($count > 0) {
        $result = $conn->query("SELECT a.id, a.title, s.subject_name, a.published_at 
                               FROM ai_assignments a 
                               LEFT JOIN subjects s ON a.subject_id = s.id 
                               WHERE a.is_published = 1 
                               LIMIT 3");
        while ($row = $result->fetch_assoc()) {
            echo "- Assignment: {$row['title']} (Subject: {$row['subject_name']}) - Published: {$row['published_at']}\n";
        }
    }
}

echo "\n=== CHECKING STUDENTS ===\n";
if ($conn->query("SHOW TABLES LIKE 'students'")->num_rows > 0) {
    $result = $conn->query("SELECT COUNT(*) as count FROM students");
    $count = $result->fetch_assoc()['count'];
    echo "Total students: $count\n";
    
    if ($count > 0) {
        echo "\nSample students:\n";
        $result = $conn->query("SELECT id, current_semester FROM students LIMIT 3");
        while ($row = $result->fetch_assoc()) {
            echo "- Student ID: {$row['id']}, Semester: {$row['current_semester']}\n";
        }
    }
}

echo "\n=== TESTING QUERY FOR STUDENT ===\n";
// Test the actual query used in the dashboard
$student_id = 1; // Test with first student
echo "Testing with student_id = $student_id\n";

$query = "SELECT q.id, q.title, s.subject_name 
          FROM ai_quizzes q 
          JOIN subjects s ON q.subject_id = s.id 
          JOIN student_enrollments se ON q.subject_id = se.subject_id 
          WHERE se.student_id = ? AND q.is_published = 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

echo "Quizzes visible to student $student_id: " . $result->num_rows . "\n";
while ($row = $result->fetch_assoc()) {
    echo "- {$row['title']} (Subject: {$row['subject_name']})\n";
}

$conn->close();
echo "\nDone!\n";
?>