-- Create faculty_departments junction table for many-to-many relationship
CREATE TABLE IF NOT EXISTS `faculty_departments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `faculty_id` INT(11) NOT NULL,
  `department_id` INT(11) NOT NULL,
  `assigned_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_faculty_department` (`faculty_id`, `department_id`),
  KEY `idx_faculty` (`faculty_id`),
  KEY `idx_department` (`department_id`),
  CONSTRAINT `fk_faculty_dept_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_faculty_dept_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Migrate existing faculty department data to the new table
INSERT INTO `faculty_departments` (`faculty_id`, `department_id`)
SELECT `id`, `department_id` 
FROM `faculty` 
WHERE `department_id` IS NOT NULL;

-- Note: We'll keep the department_id column in faculty table for backward compatibility
-- but the primary source of truth will be the faculty_departments table
