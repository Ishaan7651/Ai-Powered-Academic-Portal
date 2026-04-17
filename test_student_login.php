<?php
/**
 * Test what happens when student 3 logs in
 */

$conn = new mysqli('localhost', 'root', 'Tinkerbell8877', 'college_academic_portal');

echo "=== SIMULATING STUDENT 3 LOGIN ===\n\n";

$user_id = 3; // This maps to student_id = 1

// Get student info (user_id 3 = student_id 1)
$result = $conn->query("SELECT s.* FROM students s JOIN users u ON s.user_id = u.id WHERE u.id = $user_id");
if ($student = $result->fetch_assoc()) {
    $student_id = $student['id'];
    echo "User ID: $user_id\n";
    echo "Student ID: {$student['id']}\n";
    echo "Current Semester: {$student['current_semester']}\n";
    echo "Department ID: {$student['department_id']}\n";
} else {
    echo "Student not found!\n";
    exit;
}

// Test the exact queries from the dashboard
echo "\n=== DASHBOARD QUERIES ===\n";

// Quiz progress query (updated)
$query = "SELECT COUNT(q.id) as count
          FROM ai_quizzes q
          JOIN subjects s ON q.subject_id = s.id
          JOIN student_enrollments se ON q.subject_id = se.subject_id
          WHERE q.is_published = 1 
          AND se.student_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$total_quizzes = $result->fetch_assoc()['count'];

echo "Total published quizzes: $total_quizzes\n";

// Check if student has attempted any quizzes
$query = "SELECT COUNT(DISTINCT qa.quiz_id) as count
          FROM quiz_attempts qa
          JOIN ai_quizzes q ON qa.quiz_id = q.id
          JOIN subjects s ON q.subject_id = s.id
          JOIN student_enrollments se ON q.subject_id = se.subject_id
          WHERE qa.student_id = ? 
          AND se.student_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
$attempted_quizzes = $result->fetch_assoc()['count'];

echo "Attempted quizzes: $attempted_quizzes\n";

$course_progress = $total_quizzes > 0 ? round(($attempted_quizzes / $total_quizzes) * 100, 1) : 0;
echo "Course progress: {$course_progress}%\n";

echo "\n=== WHAT STUDENT SHOULD SEE ON DASHBOARD ===\n";
echo "- Quiz progress: {$attempted_quizzes}/{$total_quizzes} quizzes ({$course_progress}%)\n";
echo "- Links to: Question Papers, Quizzes, Assignments pages\n";
echo "- When clicking those links, they should see the published content\n";

$conn->close();
?>