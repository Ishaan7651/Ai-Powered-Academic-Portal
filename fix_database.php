<?php
/**
 * Database Fix Script
 * Fixes the ai_assignments table column issue
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

// Check if column exists
echo "Checking table structure...\n";
$check_sql = "SHOW COLUMNS FROM `ai_assignments` LIKE 'assignment_type'";
$result = $conn->query($check_sql);

if ($result->num_rows > 0) {
    // Column exists as 'assignment_type', rename it to 'type'
    echo "Found 'assignment_type' column. Renaming to 'type'...\n";
    $sql = "ALTER TABLE `ai_assignments` 
            CHANGE COLUMN `assignment_type` `type` 
            ENUM('research','essay','project','case_study','presentation') 
            DEFAULT 'research'";
} else {
    // Check if 'type' column already exists
    $check_type = "SHOW COLUMNS FROM `ai_assignments` LIKE 'type'";
    $result_type = $conn->query($check_type);
    
    if ($result_type->num_rows > 0) {
        echo "'type' column already exists. No changes needed.\n";
        $conn->close();
        exit(0);
    }
    
    // Column doesn't exist at all, add it
    echo "'assignment_type' column not found. Adding 'type' column...\n";
    $sql = "ALTER TABLE `ai_assignments` 
            ADD COLUMN `type` ENUM('research','essay','project','case_study','presentation') 
            DEFAULT 'research' 
            AFTER `title`";
}

if ($conn->query($sql) === TRUE) {
    echo "✓ Database fixed successfully!\n";
    echo "\nYou can now:\n";
    echo "1. Get new API keys from https://aistudio.google.com/app/apikey\n";
    echo "2. Update application/libraries/AI_service.php with new keys\n";
    echo "3. Restart your server and try again\n";
} else {
    echo "✗ Error: " . $conn->error . "\n";
}

$conn->close();
?>
