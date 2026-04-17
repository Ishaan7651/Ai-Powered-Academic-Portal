<?php
$conn = new mysqli('localhost', 'root', 'Tinkerbell8877', 'college_academic_portal');

$user_id = 3; // Test with student 3 who we know is enrolled

echo "=== TESTING DASHBOARD QUERIES FOR STUDENT $user_id ===\n\n";

// Test new enrollment-based quiz query
echo "1. Published quizzes (enrollment-based):\n";
$query = "SELECT COUNT(q.id) as count
          FROM ai_quizzes q
          JOIN subjects s ON q.subject_id = s.id
          JOIN student_enrollments se ON q.subject_id = se.subject_id
          WHERE q.is_published = 1 
          AND se.student_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$total_quizzes = $result->fetch_assoc()['count'];
echo "Total published quizzes visible: $total_quizzes\n";

// Test assignments
echo "\n2. Published assignments (enrollment-based):\n";
$query = "SELECT COUNT(a.id) as count
          FROM ai_assignments a
          JOIN subjects s ON a.subject_id = s.id
          JOIN student_enrollments se ON a.subject_id = se.subject_id
          WHERE a.is_published = 1 
          AND se.student_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$total_assignments = $result->fetch_assoc()['count'];
echo "Total published assignments visible: $total_assignments\n";

// Test question papers
echo "\n3. Published question papers (enrollment-based):\n";
$query = "SELECT COUNT(qp.id) as count
          FROM ai_question_papers qp
          JOIN subjects s ON qp.subject_id = s.id
          JOIN student_enrollments se ON qp.subject_id = se.subject_id
          WHERE qp.is_published = 1 
          AND se.student_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$total_papers = $result->fetch_assoc()['count'];
echo "Total published question papers visible: $total_papers\n";

echo "\n=== SUMMARY ===\n";
echo "Student $user_id should see:\n";
echo "- $total_quizzes quizzes\n";
echo "- $total_assignments assignments\n";
echo "- $total_papers question papers\n";

$conn->close();
?>