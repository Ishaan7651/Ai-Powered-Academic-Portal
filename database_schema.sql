-- College Academic Portal Database Schema
-- Requirements: 7.1 - phpMyAdmin-compatible MySQL database

CREATE DATABASE IF NOT EXISTS college_academic_portal;
USE college_academic_portal;

-- Users table for all system users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'faculty', 'student') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_username (username),
    INDEX idx_role (role),
    INDEX idx_active (is_active)
);

-- Faculty-specific information
CREATE TABLE faculty (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    employee_id VARCHAR(20) UNIQUE,
    department VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_employee_id (employee_id)
);

-- Student-specific information
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    student_id VARCHAR(20) UNIQUE,
    current_semester INT NOT NULL,
    enrollment_year YEAR,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_student_id (student_id),
    INDEX idx_semester (current_semester)
);

-- Subjects/Courses
CREATE TABLE subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subject_code VARCHAR(20) UNIQUE NOT NULL,
    subject_name VARCHAR(200) NOT NULL,
    semester INT NOT NULL,
    credits INT DEFAULT 3,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_semester (semester),
    INDEX idx_code (subject_code),
    INDEX idx_active (is_active)
);

-- Faculty-Subject assignments
CREATE TABLE faculty_subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_id INT NOT NULL,
    subject_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_assignment (faculty_id, subject_id),
    INDEX idx_faculty (faculty_id),
    INDEX idx_subject (subject_id)
);

-- Resource storage
CREATE TABLE resources (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subject_id INT NOT NULL,
    faculty_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    file_type ENUM('pdf', 'ppt', 'excel', 'csv', 'epub', 'weblink') NOT NULL,
    file_path VARCHAR(500),
    file_size BIGINT,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (faculty_id) REFERENCES faculty(id),
    INDEX idx_subject (subject_id),
    INDEX idx_faculty (faculty_id),
    INDEX idx_upload_date (upload_date),
    INDEX idx_active (is_active)
);

-- Assignments
CREATE TABLE assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subject_id INT NOT NULL,
    faculty_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    total_marks INT NOT NULL,
    due_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_ai_generated BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (faculty_id) REFERENCES faculty(id),
    INDEX idx_subject (subject_id),
    INDEX idx_faculty (faculty_id),
    INDEX idx_due_date (due_date)
);

-- Question papers
CREATE TABLE question_papers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subject_id INT NOT NULL,
    faculty_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    total_marks INT NOT NULL,
    format_template_id INT,
    generated_content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (faculty_id) REFERENCES faculty(id),
    INDEX idx_subject (subject_id),
    INDEX idx_faculty (faculty_id)
);

-- Question paper format templates
CREATE TABLE paper_formats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_id INT NOT NULL,
    template_name VARCHAR(100) NOT NULL,
    format_structure JSON,
    sample_image_path VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id),
    INDEX idx_faculty (faculty_id)
);

-- Chat sessions for document interaction
CREATE TABLE chat_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    resource_id INT NOT NULL,
    session_token VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE CASCADE,
    INDEX idx_student (student_id),
    INDEX idx_resource (resource_id),
    INDEX idx_token (session_token),
    INDEX idx_activity (last_activity)
);

-- Chat messages
CREATE TABLE chat_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT NOT NULL,
    message_type ENUM('user', 'assistant') NOT NULL,
    content TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES chat_sessions(id) ON DELETE CASCADE,
    INDEX idx_session (session_id),
    INDEX idx_timestamp (timestamp)
);

-- Insert sample admin user (password: admin123)
INSERT INTO users (username, email, password_hash, role) VALUES 
('admin', 'admin@college.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample subjects
INSERT INTO subjects (subject_code, subject_name, semester, credits) VALUES 
('CS101', 'Introduction to Computer Science', 1, 4),
('MATH101', 'Calculus I', 1, 3),
('ENG101', 'English Composition', 1, 3),
('CS201', 'Data Structures', 2, 4),
('MATH201', 'Calculus II', 2, 3),
('CS301', 'Database Systems', 3, 4),
('CS302', 'Software Engineering', 3, 4),
('CS401', 'Artificial Intelligence', 4, 4);