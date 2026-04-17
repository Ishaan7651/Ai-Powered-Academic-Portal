<?php
/**
 * Test Diya Kakde's access to published content
 */

$conn = new mysqli('localhost', 'root', 'Tinkerbell8877', 'college_academic_portal');

echo "=== TESTING DIYA KAKDE ACCESS ===\n\n";

// Find Diya Kakde's user info
$result = $conn->query("SELECT * FROM users WHERE username LIKE '%Diya%' OR username LIKE '%Kakde%'");
if ($user = $result->fetch_assoc()) {
    $user_id = $user['id'];
    echo "User: {$user['username']} (ID: $user_id)\n";
    echo "Role: {$user['role']}\n";
    echo "Active: {$user['is_active']}\n";
    
    // Get student record
    $result = $conn->query("SELECT * FROM students WHERE user_id = $user_id");
    if ($student = $result->fetch_assoc()) {
        $student_db_id = $student['id'];
        echo "\nStudent DB ID: $student_db_id\n";
        echo "Current Semester: {$student['current_semester']}\n";
        echo "Department: {$student['department_id']}\n";
        
        // Check enrollments
        echo "\nEnrollments:\n";
        $result = $conn->query("SELECT se.*, s.subject_name 
                               FROM student_enrollments se 
                               JOIN subjects s ON se.subject_id = s.id 
                               WHERE se.student_id = $student_db_id");
        $enrollment_count = 0;
        while ($row = $result->fetch_assoc()) {
            echo "- {$row['subject_name']} (ID: {$row['subject_id']})\n";
            $enrollment_count++;
        }
        
        if ($enrollment_count == 0) {
            echo "❌ NO ENROLLMENTS FOUND!\n";
        }
        
        // Test the exact query from student_question_papers page
        echo "\n=== TESTING QUESTION PAPERS QUERY ===\n";
        $query = "SELECT qp.*, s.subject_name, s.subject_code
                  FROM ai_question_papers qp
                  JOIN subjects s ON qp.subject_id = s.id
                  JOIN student_enrollments se ON qp.subject_id = se.subject_id
                  WHERE se.student_id = ? AND qp.is_published = 1
                  ORDER BY qp.published_at DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $student_db_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        echo "Question papers visible: " . $result->num_rows . "\n";
        while ($row = $result->fetch_assoc()) {
            echo "- {$row['title']} (Subject: {$row['subject_name']}) - Published: {$row['published_at']}\n";
        }
        
        // Test assignments too
        echo "\n=== TESTING ASSIGNMENTS QUERY ===\n";
        $query = "SELECT a.*, s.subject_name, s.subject_code
                  FROM ai_assignments a
                  JOIN subjects s ON a.subject_id = s.id
                  JOIN student_enrollments se ON a.subject_id = se.subject_id
                  WHERE se.student_id = ? AND a.is_published = 1
                  ORDER BY a.published_at DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $student_db_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        echo "Assignments visible: " . $result->num_rows . "\n";
        while ($row = $result->fetch_assoc()) {
            echo "- {$row['title']} (Subject: {$row['subject_name']}) - Published: {$row['published_at']}\n";
        }
        
    } else {
        echo "❌ No student record found for user $user_id\n";
    }
    
} else {
    echo "❌ User 'Diya Kakde' not found!\n";
}

$conn->close();
?>