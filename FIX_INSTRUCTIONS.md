# How to Fix the 500 Error and 403 API Error

## Problem 1: API Keys Are Leaked (403 Error)

Your Google Gemini API keys have been reported as leaked and disabled by Google.

### Solution:
1. Go to [Google AI Studio](https://aistudio.google.com/app/apikey)
2. Delete the old leaked keys
3. Create NEW API keys
4. Open `application/libraries/AI_service.php`
5. Find lines 20-24 and replace with your NEW keys:

```php
$this->api_keys = [
    'YOUR-NEW-GEMINI-API-KEY-1',
    'YOUR-NEW-GEMINI-API-KEY-2',  // Optional backup
    'YOUR-NEW-GEMINI-API-KEY-3'   // Optional backup
];
```

## Problem 2: Database Column Missing (500 Error)

The database table `ai_assignments` is missing the `assignment_type` column but your code is trying to insert into a column called `type`.

### Solution - Run this SQL:

```sql
-- Fix the ai_assignments table
ALTER TABLE `ai_assignments` 
CHANGE COLUMN `assignment_type` `type` 
ENUM('research','essay','project','case_study','presentation') 
DEFAULT 'research';
```

OR if the column doesn't exist at all:

```sql
-- Add the type column if it's completely missing
ALTER TABLE `ai_assignments` 
ADD COLUMN `type` ENUM('research','essay','project','case_study','presentation') 
DEFAULT 'research' 
AFTER `title`;
```

## How to Apply the Database Fix:

### Option 1: Using phpMyAdmin
1. Open phpMyAdmin
2. Select database `college_academic_portal`
3. Click on "SQL" tab
4. Paste the SQL command above
5. Click "Go"

### Option 2: Using Command Line
```bash
mysql -u root -p college_academic_portal
# Enter password: Tinkerbell8877
# Then paste the SQL command
```

### Option 3: Using PHP script
Create a file `fix_database.php` in your root directory:

```php
<?php
$conn = new mysqli('localhost', 'root', 'Tinkerbell8877', 'college_academic_portal');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "ALTER TABLE `ai_assignments` 
        CHANGE COLUMN `assignment_type` `type` 
        ENUM('research','essay','project','case_study','presentation') 
        DEFAULT 'research'";

if ($conn->query($sql) === TRUE) {
    echo "Database fixed successfully!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
```

Then run: `php fix_database.php`

## After Fixing:

1. Get new API keys from Google
2. Update `application/libraries/AI_service.php` with new keys
3. Run the SQL fix for the database
4. Restart your PHP server
5. Try generating an assignment again

## Test if it works:
```bash
# Restart server
php -S localhost:8000

# Then try the assignment generation feature again
```
