<?php
$conn = new mysqli('localhost', 'root', 'Tinkerbell8877', 'college_academic_portal');

echo "=== STUDENTS TABLE STRUCTURE ===\n";
$result = $conn->query("DESCRIBE students");
while ($row = $result->fetch_assoc()) {
    echo "{$row['Field']} ({$row['Type']})\n";
}

echo "\n=== SAMPLE STUDENT RECORD ===\n";
$result = $conn->query("SELECT * FROM students WHERE user_id = 3 LIMIT 1");
if ($row = $result->fetch_assoc()) {
    foreach ($row as $key => $value) {
        echo "$key: $value\n";
    }
} else {
    echo "No student found for user_id = 3\n";
}

$conn->close();
?>