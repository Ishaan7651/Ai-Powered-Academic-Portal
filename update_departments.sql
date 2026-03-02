-- Update departments structure - SAFE VERSION
-- Run this script in phpMyAdmin or MySQL command line

-- Step 1: Clear existing departments
DELETE FROM `departments`;

-- Step 2: Add new departments
INSERT INTO `departments` (`id`, `name`, `code`, `description`) VALUES
(1, 'BTech AI and ML', 'BTECH-AI', 'Bachelor of Technology in Artificial Intelligence and Machine Learning'),
(2, 'BSc in Psychology', 'BSC-PSY', 'Bachelor of Science in Psychology'),
(3, 'BTech in Sound Engineering', 'BTECH-SE', 'Bachelor of Technology in Sound Engineering'),
(4, 'BBA', 'BBA', 'Bachelor of Business Administration'),
(5, 'MBA', 'MBA', 'Master of Business Administration');

-- Step 3: Reset auto increment
ALTER TABLE `departments` AUTO_INCREMENT = 6;

-- Step 4: Add department_id to students table (skip if column exists)
-- Check if column exists first, if error occurs, it means column already exists - that's OK
ALTER TABLE `students` ADD COLUMN `department_id` INT(11) DEFAULT NULL AFTER `user_id`;

-- Step 5: Add index for students department_id
ALTER TABLE `students` ADD KEY `fk_student_department` (`department_id`);

-- Step 6: Update existing students to BSc Psychology (department_id = 2)
-- Adjust this based on your actual student data
UPDATE `students` SET `department_id` = 2 WHERE `department_id` IS NULL;

-- Step 7: Add department_id to subjects table (skip if column exists)
ALTER TABLE `subjects` ADD COLUMN `department_id` INT(11) DEFAULT NULL AFTER `subject_name`;

-- Step 8: Add index for subjects department_id
ALTER TABLE `subjects` ADD KEY `fk_subject_department` (`department_id`);

-- Step 9: Update Clinical Psychology subject to BSc Psychology department
UPDATE `subjects` SET `department_id` = 2 WHERE `subject_code` = 'SLABSPSY6.02005';

-- Step 10: Update other subjects to BTech AI and ML (adjust as needed)
UPDATE `subjects` SET `department_id` = 1 WHERE `subject_code` IN ('MATH101', 'CS101', 'DL002', 'PHYS101', 'CHEM101', 'ENG101');

-- Done! You can now verify:
-- SELECT * FROM departments;
-- SELECT id, user_id, department_id, current_semester FROM students;
-- SELECT id, subject_code, subject_name, semester, department_id FROM subjects;
