<?php
/**
 * Test the final dashboard fix
 */

$conn = new mysqli('localhost', 'root', 'Tinkerbell8877', 'college_academic_portal');

$user_id = 3; // student_demo user

echo "=== FINAL DASHBOARD TEST ===\n\n";

// Simulate the exact dashboard query
$student_query = $conn->query("
    SELECT s.*, u.email 
    FROM students s 
    JOIN users u ON s.user_id = u.id 
    WHERE s.user_id = $user_id AND u.is_active = 1
");

if ($student_data = $student_query->fetch_assoc()) {
    $student_db_id = $student_data['id']; // This is what we need for enrollments
    $current_semester = $student_data['current_semester'];
    $student_id = $student_data['student_id']; // This is just the student number
    
    echo "User ID: $user_id\n";
    echo "Student DB ID: $student_db_id (used for enrollments)\n";
    echo "Student Number: $student_id\n";
    echo "Current Semester: $current_semester\n";
    
    // Test the fixed quiz query
    $query = "SELECT COUNT(q.id) as count
              FROM ai_quizzes q
              JOIN subjects s ON q.subject_id = s.id
              JOIN student_enrollments se ON q.subject_id = se.subject_id
              WHERE q.is_published = 1 
              AND se.student_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_db_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_quizzes = $result->fetch_assoc()['count'];
    
    echo "\nPublished quizzes visible: $total_quizzes\n";
    
    // Test assignments
    $query = "SELECT COUNT(a.id) as count
              FROM ai_assignments a
              JOIN subjects s ON a.subject_id = s.id
              JOIN student_enrollments se ON a.subject_id = se.subject_id
              WHERE a.is_published = 1 
              AND se.student_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_db_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_assignments = $result->fetch_assoc()['count'];
    
    echo "Published assignments visible: $total_assignments\n";
    
    // Test question papers
    $query = "SELECT COUNT(qp.id) as count
              FROM ai_question_papers qp
              JOIN subjects s ON qp.subject_id = s.id
              JOIN student_enrollments se ON qp.subject_id = se.subject_id
              WHERE qp.is_published = 1 
              AND se.student_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_db_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_papers = $result->fetch_assoc()['count'];
    
    echo "Published question papers visible: $total_papers\n";
    
    echo "\n=== DASHBOARD SHOULD NOW SHOW ===\n";
    echo "- Quiz progress: 0/$total_quizzes quizzes (0%)\n";
    echo "- Links to pages that will show:\n";
    echo "  * $total_assignments assignments\n";
    echo "  * $total_papers question papers\n";
    echo "  * $total_quizzes quizzes\n";
    
} else {
    echo "Student not found!\n";
}

$conn->close();
?>